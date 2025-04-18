<?php

namespace Platform\Config\Database\Seeders;

use Illuminate\Database\Seeder;
use Platform\Config\Models\Configuration;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            // App configurations
            [
                'key' => 'app.name',
                'value' => 'Laravel Starter Kit',
                'group' => 'app',
                'type' => 'string',
                'description' => 'The name of the application',
                'is_system' => true,
                'is_public' => true,
            ],
            [
                'key' => 'app.env',
                'value' => 'production',
                'group' => 'app',
                'type' => 'string',
                'description' => 'The environment the application is running in',
                'is_system' => true,
                'is_public' => false,
            ],
            [
                'key' => 'app.debug',
                'value' => false,
                'group' => 'app',
                'type' => 'boolean',
                'description' => 'Whether the application is in debug mode',
                'is_system' => true,
                'is_public' => false,
            ],
            [
                'key' => 'app.url',
                'value' => 'http://localhost',
                'group' => 'app',
                'type' => 'string',
                'description' => 'The URL of the application',
                'is_system' => true,
                'is_public' => true,
            ],
            
            // Mail configurations
            [
                'key' => 'mail.driver',
                'value' => 'smtp',
                'group' => 'mail',
                'type' => 'string',
                'description' => 'The mail driver to use',
                'is_system' => true,
                'is_public' => false,
            ],
            [
                'key' => 'mail.host',
                'value' => 'smtp.mailtrap.io',
                'group' => 'mail',
                'type' => 'string',
                'description' => 'The mail server host',
                'is_system' => true,
                'is_public' => false,
            ],
            [
                'key' => 'mail.port',
                'value' => 2525,
                'group' => 'mail',
                'type' => 'integer',
                'description' => 'The mail server port',
                'is_system' => true,
                'is_public' => false,
            ],
            [
                'key' => 'mail.from.address',
                'value' => 'noreply@example.com',
                'group' => 'mail',
                'type' => 'string',
                'description' => 'The from address for all emails',
                'is_system' => true,
                'is_public' => false,
            ],
            [
                'key' => 'mail.from.name',
                'value' => 'Laravel Starter Kit',
                'group' => 'mail',
                'type' => 'string',
                'description' => 'The from name for all emails',
                'is_system' => true,
                'is_public' => false,
            ],
            
            // Database configurations
            [
                'key' => 'database.default',
                'value' => 'mysql',
                'group' => 'database',
                'type' => 'string',
                'description' => 'The default database connection',
                'is_system' => true,
                'is_public' => false,
            ],
            
            // General settings
            [
                'key' => 'general.site_logo',
                'value' => '/images/logo.png',
                'group' => 'general',
                'type' => 'string',
                'description' => 'The site logo path',
                'is_system' => false,
                'is_public' => true,
            ],
            [
                'key' => 'general.site_favicon',
                'value' => '/images/favicon.ico',
                'group' => 'general',
                'type' => 'string',
                'description' => 'The site favicon path',
                'is_system' => false,
                'is_public' => true,
            ],
            [
                'key' => 'general.social_links',
                'value' => [
                    'facebook' => 'https://facebook.com/laravelstarterkit',
                    'twitter' => 'https://twitter.com/laravelstarterkit',
                    'instagram' => 'https://instagram.com/laravelstarterkit',
                ],
                'group' => 'general',
                'type' => 'json',
                'description' => 'Social media links',
                'is_system' => false,
                'is_public' => true,
            ],
            [
                'key' => 'general.maintenance_mode',
                'value' => false,
                'group' => 'general',
                'type' => 'boolean',
                'description' => 'Whether the site is in maintenance mode',
                'is_system' => true,
                'is_public' => true,
            ],
        ];

        foreach ($configurations as $config) {
            Configuration::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}
