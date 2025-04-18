# Admin Module

The Admin module provides a comprehensive administration panel for the Laravel Starter Kit. It includes user management, role-based access control, permissions, and system settings.

## Features

- **Authentication**: Secure admin login system separate from regular users
- **Dashboard**: Overview of system statistics and recent activity
- **User Management**: Create, view, edit, and delete user accounts
- **Role Management**: Create and manage roles with specific permissions
- **Permission System**: Fine-grained access control for admin users
- **Settings**: Configure system-wide settings

## Installation

This module is included by default in the Laravel Starter Kit. If you're adding it to an existing project, follow these steps:

1. Add the module to your `composer.json` autoload section:

   ```json
   "autoload": {
       "psr-4": {
           "Platform\\Admin\\": "platform/Admin/src/"
       }
   }
   ```

2. Run `composer dump-autoload`

3. Register the service provider in `config/app.php`:

   ```php
   'providers' => [
       // Other service providers...
       Platform\Admin\Providers\AdminServiceProvider::class,
   ],
   ```

4. Run migrations to create the necessary database tables:

   ```bash
   php artisan migrate
   ```

5. Seed the database with initial admin user, roles, and permissions:

   ```bash
   php artisan db:seed --class="Platform\\Admin\\Database\\Seeders\\AdminSeeder"
   ```

## Usage

### Accessing the Admin Panel

The admin panel is accessible at `/admin`. The default credentials are:

- Email: `admin@example.com`
- Password: `password`

It's recommended to change these credentials after first login.

### Authentication

The Admin module uses a separate authentication guard named `admin`. To authenticate admin users in your code:

```php
Auth::guard('admin')->attempt($credentials);
```

### Middleware

The module provides several middleware for protecting admin routes:

- `admin.auth`: Ensures the user is authenticated as an admin
- `admin.role:role_name`: Ensures the admin has a specific role
- `admin.permission:permission_name`: Ensures the admin has a specific permission

Example usage in routes:

```php
Route::middleware(['admin.auth', 'admin.permission:user.view'])->group(function () {
    // Routes that require admin authentication and 'user.view' permission
});
```

### Roles and Permissions

The module includes a role-based access control system with predefined roles:

- **Super Admin**: Has all permissions
- **Editor**: Has content management permissions
- **Moderator**: Has user viewing and content permissions

You can create custom roles and assign specific permissions through the admin interface.

## Customization

### Views

To customize the views, publish them to your application:

```bash
php artisan vendor:publish --tag=admin-views
```

### Configuration

To customize the configuration, publish the config file:

```bash
php artisan vendor:publish --tag=admin-config
```

### Assets

To customize the assets, publish them to your public directory:

```bash
php artisan vendor:publish --tag=admin-assets
```

## License

This module is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
