<?php namespace Api\Parts;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;


class PartServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
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

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (!app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/Database/factories');
        }
    }
}
