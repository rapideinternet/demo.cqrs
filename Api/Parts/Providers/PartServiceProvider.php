<?php namespace Api\Parts;

use Api\Parts\Commands\Handlers\PartCommandHandler;
use Api\Parts\Console\ReplayPartsCommand;
use Api\Parts\ReadModel\PartsThatWereManufacturedProjector;
use Api\Parts\Repositories\ElasticSearchReadModelPartRepository;
use Api\Parts\Repositories\MysqlEventStorePartRepository;
use Elasticsearch\Client;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class PartServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->bindEventSourcedRepositories();
        $this->bindReadModelRepositories();

        $this->registerCommandSubscribers();
        $this->registerEventSubscribers();

        $this->registerConsoleCommands();

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();

        include_once __DIR__ . '/../Http/routes.php';
    }


    public function boot()
    {
    }

    /**
     * Bind repositories
     */
    private function bindEventSourcedRepositories()
    {
        $this->app->bind(\Api\Parts\Repositories\EventStorePartRepository::class, function ($app) {
            $eventStore = $app[\Broadway\EventStore\EventStore::class];
            $eventBus = $app[\Broadway\EventHandling\EventBus::class];

            return new MysqlEventStorePartRepository($eventStore, $eventBus, $app[\Doctrine\DBAL\Connection::class]);
        });
    }

    /**
     * Bind the read model repositories in the IoC container
     */
    private function bindReadModelRepositories()
    {
        $this->app->bind(\Api\Parts\Repositories\ReadModelPartRepository::class, function ($app) {
            $serializer = $app[\Broadway\Serializer\Serializer::class];

            return new ElasticSearchReadModelPartRepository($app[Client::class], $serializer);
        });
    }

    /**
     * Register the command handlers on the command bus
     */
    private function registerCommandSubscribers()
    {
        $partCommandHandler = new PartCommandHandler($this->app[\Api\Parts\Repositories\EventStorePartRepository::class]);

        $this->app['laravelbroadway.command.registry']->subscribe([
            $partCommandHandler
        ]);
    }

    /**
     * Register the event listeners on the event bus
     */
    private function registerEventSubscribers()
    {
        $partsThatWereManfacturedProjector = new PartsThatWereManufacturedProjector($this->app[\Api\Parts\Repositories\ReadModelPartRepository::class]);

        $this->app['laravelbroadway.event.registry']->subscribe([
            $partsThatWereManfacturedProjector
        ]);
    }


    /**
     * Register the filters.
     *
     * @param  Router $router
     * @return void
     */
    public function registerMiddleware(Router $router)
    {
        foreach ($this->middleware as $module => $middlewares) {
            foreach ($middlewares as $name => $middleware) {
                $class = "Modules\\{$module}\\Http\\Middleware\\{$middleware}";
                $router->middleware($name, $class);
            }
        }
    }

    private function registerConsoleCommands()
    {
        $this->app->singleton('command.event.replay.parts', function ($app) {
            $eventStorePartRepository = $app[\Api\Parts\Repositories\EventStorePartRepository::class];
            $eventBus = $app[\Broadway\EventHandling\EventBus::class];

            return new ReplayPartsCommand($eventStorePartRepository, $eventBus);
        });
        $this->commands([
            'command.event.replay.parts',
        ]);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('parts.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', 'parts'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/parts');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/parts';
        }, Config::get('view.paths')), [$sourcePath]), 'parts');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/parts');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'parts');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'parts');
        }
    }
}
