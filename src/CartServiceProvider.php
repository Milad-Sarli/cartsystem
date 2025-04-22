<?php

namespace MiladSarli\CartSystem;

use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/cart.php', 'cart'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/cart.php' => config_path('cart.php'),
        ], 'cart-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'cart-migrations');

        // Publish models
        $this->publishes([
            __DIR__.'/Models' => app_path('Models/Cart'),
        ], 'cart-models');

        // Publish controllers
        $this->publishes([
            __DIR__.'/Http/Controllers' => app_path('Http/Controllers/Cart'),
        ], 'cart-controllers');

        // Publish routes
        $this->publishes([
            __DIR__.'/../routes' => base_path('routes/cart'),
        ], 'cart-routes');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');


        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class
            ]);
        }
    }
}
