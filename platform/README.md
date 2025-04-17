# Laravel Modular Architecture

This directory contains the modular architecture for the Laravel Starter Kit. Each module is a self-contained component that can be developed, tested, and maintained independently.

## Structure

The modular architecture is organized as follows:

- `platform/`: The root directory for all modules
  - `Core/`: The core module that provides the foundation for all other modules
  - `User/`: An example module for user management
  - Other modules...

## Creating a New Module

You can create a new module using the provided Artisan command:

```bash
php artisan make:module ModuleName
```

This will create a new module with the basic structure. You can also use the following options:

- `--all`: Generate a module with all components
- `--controller`: Generate a controller for the module
- `--model`: Generate a model for the module
- `--migration`: Generate a migration for the module
- `--api`: Generate an API controller for the module
- `--view`: Generate views for the module
- `--force`: Force the operation to run when the module already exists

Example:

```bash
php artisan make:module Blog --all
```

## Module Structure

Each module follows this structure:

```
ModuleName/
├── composer.json         # Module's composer configuration
├── database/
│   └── migrations/       # Database migrations
├── resources/
│   └── views/            # Module views
├── routes/
│   ├── api.php           # API routes
│   └── web.php           # Web routes
└── src/
    ├── Http/
    │   └── Controllers/  # Controllers
    │       └── Api/       # API controllers
    ├── Models/           # Models
    └── Providers/        # Service providers
```

## Registering a Module

After creating a new module, you need to add it to the main `composer.json` file in the autoload section:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/",
        "Platform\\Core\\": "platform/Core/src/",
        "Platform\\User\\": "platform/User/src/",
        "Platform\\YourNewModule\\": "platform/YourNewModule/src/"
    }
}
```

Then run:

```bash
composer dump-autoload
```

## Module Development

When developing a module, follow these guidelines:

1. Keep the module self-contained
2. Use dependency injection to interact with other modules
3. Define clear interfaces for module interactions
4. Use events for loose coupling between modules

## Using the Modular Architecture

The modular architecture allows you to:

1. Organize your code into logical, self-contained units
2. Develop and test modules independently
3. Reuse modules across different projects
4. Scale your application by adding new modules
5. Maintain a clean separation of concerns
