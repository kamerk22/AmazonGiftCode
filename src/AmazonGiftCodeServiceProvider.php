<?php

namespace kamerk22\AmazonGiftCode;

use Illuminate\Support\ServiceProvider;

class AmazonGiftCodeServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'kamerk22');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'kamerk22');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/amazongiftcode.php', 'amazongiftcode');

        // Register the service the package provides.
        $this->app->singleton('amazongiftcode', function ($app) {
            return new AmazonGiftCode;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['amazongiftcode'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/amazongiftcode.php' => config_path('amazongiftcode.php'),
        ], 'amazongiftcode.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/kamerk22'),
        ], 'amazongiftcode.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/kamerk22'),
        ], 'amazongiftcode.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/kamerk22'),
        ], 'amazongiftcode.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
