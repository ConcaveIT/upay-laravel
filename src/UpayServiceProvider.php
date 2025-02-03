<?php

namespace Concaveit\Upay;

use Illuminate\Support\ServiceProvider;

class UpayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot()
    {
        // Publish the configuration file.
        $this->publishes([
            __DIR__.'/../config/upay.php' => config_path('upay.php'),
        ], 'config');
    }

    /**
     * Register the service in the container.
     */
    public function register()
    {
        // Merge default config.
        $this->mergeConfigFrom(
            __DIR__.'/../config/upay.php',
            'upay'
        );

        // Register the Upay service as a singleton.
        $this->app->singleton('upay', function ($app) {
            return new Upay(config('upay'));
        });
    }
}
