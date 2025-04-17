<?php

namespace Platform\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {name : The name of the module}'
                           . ' {--force : Force the operation to run when the module already exists}'
                           . ' {--all : Generate a module with all components}'
                           . ' {--controller : Generate a controller for the module}'
                           . ' {--model : Generate a model for the module}'
                           . ' {--migration : Generate a migration for the module}'
                           . ' {--api : Generate an API controller for the module}'
                           . ' {--view : Generate views for the module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $force = $this->option('force');
        
        // Convert to StudlyCase for class names, lowercase for paths
        $studlyName = Str::studly($name);
        $lowerName = Str::lower($name);
        
        $modulePath = base_path('platform/' . $studlyName);
        
        // Check if module exists
        if (File::exists($modulePath) && !$force) {
            $this->error("Module [{$studlyName}] already exists!");
            return 1;
        }
        
        // Create module directories
        $this->createDirectories($studlyName);
        
        // Create module files
        $this->createModuleFiles($studlyName, $lowerName);
        
        // Create additional components based on options
        if ($this->option('all') || $this->option('controller')) {
            $this->createController($studlyName);
        }
        
        if ($this->option('all') || $this->option('model')) {
            $this->createModel($studlyName);
        }
        
        if ($this->option('all') || $this->option('migration')) {
            $this->createMigration($studlyName, $lowerName);
        }
        
        if ($this->option('all') || $this->option('api')) {
            $this->createApiController($studlyName);
        }
        
        if ($this->option('all') || $this->option('view')) {
            $this->createViews($studlyName, $lowerName);
        }
        
        $this->info("Module [{$studlyName}] created successfully.");
        $this->info("Remember to add 'Platform\\{$studlyName}\\': 'platform/{$studlyName}/src/' to your composer.json autoload section.");
        
        return 0;
    }
    
    /**
     * Create module directories
     */
    protected function createDirectories(string $name): void
    {
        $directories = [
            base_path("platform/{$name}/src/Providers"),
            base_path("platform/{$name}/src/Models"),
            base_path("platform/{$name}/src/Http/Controllers"),
            base_path("platform/{$name}/src/Http/Controllers/Api"),
            base_path("platform/{$name}/database/migrations"),
            base_path("platform/{$name}/routes"),
            base_path("platform/{$name}/resources/views"),
        ];
        
        foreach ($directories as $directory) {
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
        }
    }
    
    /**
     * Create module files
     */
    protected function createModuleFiles(string $name, string $lowerName): void
    {
        // Create composer.json
        $composerJson = $this->getComposerStub($name);
        File::put(base_path("platform/{$name}/composer.json"), $composerJson);
        
        // Create service provider
        $serviceProvider = $this->getServiceProviderStub($name, $lowerName);
        File::put(base_path("platform/{$name}/src/Providers/{$name}ServiceProvider.php"), $serviceProvider);
        
        // Create routes files
        $webRoutes = $this->getWebRoutesStub($name);
        File::put(base_path("platform/{$name}/routes/web.php"), $webRoutes);
        
        $apiRoutes = $this->getApiRoutesStub($name);
        File::put(base_path("platform/{$name}/routes/api.php"), $apiRoutes);
    }
    
    /**
     * Create controller
     */
    protected function createController(string $name): void
    {
        $controller = $this->getControllerStub($name);
        File::put(base_path("platform/{$name}/src/Http/Controllers/{$name}Controller.php"), $controller);
    }
    
    /**
     * Create API controller
     */
    protected function createApiController(string $name): void
    {
        $controller = $this->getApiControllerStub($name);
        File::put(base_path("platform/{$name}/src/Http/Controllers/Api/{$name}Controller.php"), $controller);
    }
    
    /**
     * Create model
     */
    protected function createModel(string $name): void
    {
        $model = $this->getModelStub($name);
        File::put(base_path("platform/{$name}/src/Models/{$name}.php"), $model);
    }
    
    /**
     * Create migration
     */
    protected function createMigration(string $name, string $tableName): void
    {
        $timestamp = date('Y_m_d_His');
        $migration = $this->getMigrationStub($name, $tableName);
        File::put(base_path("platform/{$name}/database/migrations/{$timestamp}_create_{$tableName}s_table.php"), $migration);
    }
    
    /**
     * Create views
     */
    protected function createViews(string $name, string $viewName): void
    {
        $index = $this->getViewStub($name, $viewName);
        File::put(base_path("platform/{$name}/resources/views/index.blade.php"), $index);
    }
    
    /**
     * Get composer.json stub
     */
    protected function getComposerStub(string $name): string
    {
        $stub = File::get(__DIR__ . '/stubs/composer.stub');
        
        return str_replace(
            ['{module}', '{Module}'],
            [strtolower($name), $name],
            $stub
        );
    }
    
    /**
     * Get service provider stub
     */
    protected function getServiceProviderStub(string $name, string $lowerName): string
    {
        $stub = File::get(__DIR__ . '/stubs/provider.stub');
        
        return str_replace(
            ['{module}', '{Module}'],
            [$lowerName, $name],
            $stub
        );
    }
    
    /**
     * Get web routes stub
     */
    protected function getWebRoutesStub(string $name): string
    {
        $stub = File::get(__DIR__ . '/stubs/routes.web.stub');
        
        return str_replace(
            ['{Module}'],
            [$name],
            $stub
        );
    }
    
    /**
     * Get API routes stub
     */
    protected function getApiRoutesStub(string $name): string
    {
        $stub = File::get(__DIR__ . '/stubs/routes.api.stub');
        
        return str_replace(
            ['{Module}'],
            [$name],
            $stub
        );
    }
    
    /**
     * Get controller stub
     */
    protected function getControllerStub(string $name): string
    {
        $stub = File::get(__DIR__ . '/stubs/controller.stub');
        
        return str_replace(
            ['{Module}'],
            [$name],
            $stub
        );
    }
    
    /**
     * Get API controller stub
     */
    protected function getApiControllerStub(string $name): string
    {
        $stub = File::get(__DIR__ . '/stubs/controller.api.stub');
        
        return str_replace(
            ['{Module}'],
            [$name],
            $stub
        );
    }
    
    /**
     * Get model stub
     */
    protected function getModelStub(string $name): string
    {
        $stub = File::get(__DIR__ . '/stubs/model.stub');
        
        return str_replace(
            ['{Module}'],
            [$name],
            $stub
        );
    }
    
    /**
     * Get migration stub
     */
    protected function getMigrationStub(string $name, string $tableName): string
    {
        $stub = File::get(__DIR__ . '/stubs/migration.stub');
        
        return str_replace(
            ['{Module}', '{table}'],
            [$name, $tableName],
            $stub
        );
    }
    
    /**
     * Get view stub
     */
    protected function getViewStub(string $name, string $viewName): string
    {
        $stub = File::get(__DIR__ . '/stubs/view.stub');
        
        return str_replace(
            ['{module}', '{Module}'],
            [$viewName, $name],
            $stub
        );
    }
}
