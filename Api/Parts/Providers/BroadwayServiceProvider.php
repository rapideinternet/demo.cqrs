<?php namespace Api\Parts;

use Api\Parts\Commands\Handlers\PartCommandHandler;
use Api\Parts\Console\ReplayPartsCommand;
use Api\Parts\Metadata\IpEnricher;
use Api\Parts\Processors\LogEntryProcessor;
use Api\Parts\ReadModel\PartsThatWereManufacturedProjector;
use Api\Parts\Repositories\EventStore\Mysql\PartRepository as MysqlPartRepository;
use Api\Parts\Repositories\ReadModel\Elasticsearch\PartRepository as ElasticSearchPartRepository;
use Broadway\EventSourcing\EventStreamDecorator;
use Elasticsearch\Client;
use Illuminate\Support\ServiceProvider;

class BroadwayServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->registerEnrichers();
        $this->bindEventSourcedRepositories();
        $this->bindReadModelRepositories();
        $this->registerCommandSubscribers();
        $this->registerEventSubscribers();
        $this->registerProcessors();
        $this->registerConsoleCommands();
    }

    /**
     * Bind repositories
     */
    private function bindEventSourcedRepositories()
    {
        $this->app->bind(\Api\Parts\Repositories\EventStore\PartRepository::class, function ($app) {
            $eventStore = $app[\Broadway\EventStore\EventStore::class];
            $eventBus = $app[\Broadway\EventHandling\EventBus::class];

            return new MysqlPartRepository(
                $eventStore,
                $eventBus,
                $app[\Doctrine\DBAL\Connection::class],
                [$app[EventStreamDecorator::class]]);
        });
    }

    /**
     * Bind the read model repositories in the IoC container
     */
    private function bindReadModelRepositories()
    {
        $this->app->bind(\Api\Parts\Repositories\ReadModel\PartRepository::class, function ($app) {
            $serializer = $app[\Broadway\Serializer\Serializer::class];

            return new ElasticSearchPartRepository($app[Client::class], $serializer);
        });
    }

    /**
     * Register the command handlers on the command bus
     */
    private function registerCommandSubscribers()
    {
        $partCommandHandler = new PartCommandHandler(
            $this->app[\Api\Parts\Repositories\EventStore\PartRepository::class]
        );

        $this->app['laravelbroadway.command.registry']->subscribe([
            $partCommandHandler
        ]);
    }

    /**
     * Register the event listeners on the event bus
     */
    private function registerEventSubscribers()
    {
        $partsThatWereManfacturedProjector = new PartsThatWereManufacturedProjector(
            $this->app[\Api\Parts\Repositories\ReadModel\PartRepository::class]
        );

        $this->app['laravelbroadway.event.registry']->subscribe([
            $partsThatWereManfacturedProjector
        ]);
    }

    /**
     * Register the event listeners on the event bus
     */
    private function registerProcessors()
    {
        //Disable processors in console mode
        if (!$this->app->runningInConsole()) {
            $logEntryProcessor = new LogEntryProcessor();

            $this->app['laravelbroadway.event.registry']->subscribe([
                $logEntryProcessor
            ]);
        }
    }

    private function registerConsoleCommands()
    {
        $this->app->singleton('command.event.replay.parts', function ($app) {
            $eventStore = $app[\Api\Parts\Repositories\EventStore\Mysql\PartRepository::class];
            $eventBus = $app[\Broadway\EventHandling\EventBus::class];

            return new ReplayPartsCommand($eventStore, $eventBus);
        });
        $this->commands([
            'command.event.replay.parts',
        ]);
    }

    /**
     * Register the Metadata enrichers
     */
    private function registerEnrichers()
    {
        $enricher = new IpEnricher();
        $this->app['laravelbroadway.enricher.registry']->subscribe([$enricher]);
    }
}
