<?php

namespace MiladSarli\CartSystem;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'cart:install';
    protected $description = 'Install the Cart System package';

    public function handle()
    {
        $this->info('Installing Cart System...');

        if ($this->confirm('Would you like to publish all Cart System files? This will publish config, migrations, models, and controllers.', true)) {
            $this->call('vendor:publish', [
                '--provider' => 'MiladSarli\CartSystem\CartServiceProvider',
                '--force' => true
            ]);

            $this->info('All files have been published successfully!');

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
        }

        $this->info('Cart System installation completed!');
    }
}