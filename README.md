# Laravel 12 Starter Kit

![Laravel Logo](https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)

[![Build Status](https://github.com/laravel/framework/workflows/tests/badge.svg)](https://github.com/laravel/framework/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel/framework)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://img.shields.io/packagist/v/laravel/framework)](https://packagist.org/packages/laravel/framework)
[![License](https://img.shields.io/packagist/l/laravel/framework)](https://packagist.org/packages/laravel/framework)

A comprehensive starter kit for Laravel 12 applications with a modular architecture, pre-configured with essential packages and best practices.

## Features

- **Laravel 12**: Built on the latest Laravel framework
- **Modular Architecture**: Organize your code into reusable, self-contained modules
- **Authentication**: Pre-configured authentication system with Laravel Sanctum
- **API Ready**: API scaffolding with versioning and documentation
- **Development Tools**: Laravel Telescope, Horizon, and Pulse for monitoring and debugging
- **Social Authentication**: Integration with Laravel Socialite
- **Feature Flags**: Using Laravel Pennant for feature toggling
- **Real-time**: Laravel Reverb for WebSockets and real-time features
- **Search**: Laravel Scout for full-text search capabilities
- **Frontend**: Livewire Volt and Flux for reactive UI components

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js and NPM
- Database (MySQL, PostgreSQL, SQLite)

## Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/laravel-starter-kit.git
cd laravel-starter-kit

# Install dependencies
composer install
npm install

# Set up environment file
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Build assets
npm run build
```

## Development

```bash
# Start the development server
composer dev
```

This will start the Laravel development server, queue worker, logs, and Vite in a single command.

## Module Development

This starter kit uses a modular architecture. See the [Module Documentation](platform/README.md) for details on creating and using modules.

```bash
# Create a new module
php artisan make:module Blog --all
```

## Testing

```bash
composer test
```

## Documentation

For detailed documentation on Laravel 12, visit the [official Laravel documentation](https://laravel.com/docs).

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
