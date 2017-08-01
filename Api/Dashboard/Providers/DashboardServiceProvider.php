<?php namespace Api\Dashboard\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerViews();

        include_once __DIR__ . '/../Http/routes.php';
    }

    public function boot()
    {
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/dashboard');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/dashboard';
        }, Config::get('view.paths')), [$sourcePath]), 'dashboard');
    }
}
