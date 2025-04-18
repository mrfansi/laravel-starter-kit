<?php

namespace Platform\Config\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallConfigModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and configure the Config module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Installing Config Module...');

        // Run migrations
        $this->info('Running migrations...');
        Artisan::call('migrate', ['--path' => 'platform/Config/database/migrations']);
        $this->info('Migrations completed.');

        // Seed the database
        if ($this->confirm('Do you want to seed the database with default configurations?', true)) {
            $this->info('Seeding database...');
            Artisan::call('db:seed', ['--class' => 'Platform\\Config\\Database\\Seeders\\ConfigurationSeeder']);
            $this->info('Database seeded.');
        }

        // Sync environment variables
        if ($this->confirm('Do you want to import configurations from your .env file?', true)) {
            $this->info('Importing configurations from .env file...');
            Artisan::call('config:sync', ['--direction' => 'env-to-db']);
            $this->info('Configurations imported.');
        }

        $this->info('Config Module has been installed successfully!');
        $this->info('You can now access the configuration management at /admin/config');
        
        return 0;
    }
}
