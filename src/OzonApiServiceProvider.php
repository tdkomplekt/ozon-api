<?php

namespace Tdkomplekt\OzonApi;

use Illuminate\Support\ServiceProvider;
use Tdkomplekt\OzonApi\Console\Commands\TablesRefresh;
use Tdkomplekt\OzonApi\Console\Commands\SyncAttributes;
use Tdkomplekt\OzonApi\Console\Commands\SyncCategories;
use Tdkomplekt\OzonApi\Console\Commands\SyncOptions;

class OzonApiServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'tdkomplekt');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'tdkomplekt');
         $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();

            $this->commands([
                TablesRefresh::class,
                SyncCategories::class,
                SyncAttributes::class,
                SyncOptions::class,
            ]);

        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ozon-api.php', 'ozon-api');

        // Register the service the package provides.
        $this->app->singleton('ozon-api', function ($app) {
            return new OzonApi();
        });

        require_once __DIR__. '/Helpers/OzonHelper.php';
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['ozon-api'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/ozon-api.php' => config_path('ozon-api.php'),
        ], 'ozon-api.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/tdkomplekt'),
        ], 'ozon-api.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/tdkomplekt'),
        ], 'ozon-api.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/tdkomplekt'),
        ], 'ozon-api.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
