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

    }
}
