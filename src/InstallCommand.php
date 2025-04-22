<?php

namespace MiladSarli\CartSystem;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'cart:install';
    protected $description = 'Install the Cart System package';

    public function handle()
    {
        $this->info('Installing Cart System...');

        if ($this->confirm('Would you like to publish all Cart System files? This will publish config, migrations, models, controllers and routes.', true)) {
            $this->call('vendor:publish', [
                '--provider' => 'MiladSarli\CartSystem\CartServiceProvider',
                '--force' => true
            ]);

            $this->info('All files have been published successfully!');

            // Add route include to api.php
            $this->addRouteInclude();

            if ($this->confirm('Would you like to run migrations now?', true)) {
                $this->call('migrate');
            }
        } else {
            $this->info('You can publish files later using:');
            $this->line('php artisan vendor:publish --provider="MiladSarli\CartSystem\CartServiceProvider" --force');
            $this->line('Or publish specific parts using tags:');
            $this->line('php artisan vendor:publish --tag=cart-config');
            $this->line('php artisan vendor:publish --tag=cart-migrations');
            $this->line('php artisan vendor:publish --tag=cart-models');
            $this->line('php artisan vendor:publish --tag=cart-controllers');
            $this->line('php artisan vendor:publish --tag=cart-routes');
        }

        $this->info('Cart System installation completed!');
        $this->info('Please make sure to include cart routes in your routes/api.php file:');
        $this->line("require base_path('routes/cart/api.php');");
    }

    protected function addRouteInclude()
    {
        $apiFile = base_path('routes/api.php');

        if (File::exists($apiFile)) {
            $contents = File::get($apiFile);

            // Check if route include already exists
            if (!str_contains($contents, "require base_path('routes/cart/api.php');")) {
                File::append($apiFile, "\n// Cart System Routes\nrequire base_path('routes/cart/api.php');\n");
                $this->info('Routes have been added to routes/api.php');
            }
        }
    }
}