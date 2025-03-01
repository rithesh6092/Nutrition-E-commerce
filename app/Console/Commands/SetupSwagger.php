<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupSwagger extends Command
{
    protected $signature = 'swagger:setup';
    protected $description = 'Setup Swagger assets and documentation';

    public function handle()
    {
        $this->info('Setting up Swagger...');

        // Publish config and views
        $this->call('vendor:publish', [
            '--provider' => "L5Swagger\L5SwaggerServiceProvider",
            '--tag' => 'l5-swagger',
            '--force' => true
        ]);

        // Publish assets
        $this->call('vendor:publish', [
            '--provider' => "L5Swagger\L5SwaggerServiceProvider",
            '--tag' => 'assets',
            '--force' => true
        ]);

        // Create directory if it doesn't exist
        if (!File::exists(public_path('vendor/l5-swagger'))) {
            File::makeDirectory(public_path('vendor/l5-swagger'), 0755, true);
        }

        // Copy assets
        File::copyDirectory(
            base_path('vendor/swagger-api/swagger-ui/dist'),
            public_path('vendor/l5-swagger')
        );

        // Generate documentation
        $this->call('l5-swagger:generate');

        $this->info('Swagger setup completed!');
    }
} 