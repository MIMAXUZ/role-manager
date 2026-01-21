# Simple Role Manager

An easy and flexible Laravel authorization and roles permission management.

[![Total Downloads](https://img.shields.io/packagist/dt/mimaxuz/role-manager.svg?style=flat-square)](https://packagist.org/packages/mimaxuz/role-manager)
[![License](https://img.shields.io/packagist/l/mimaxuz/role-manager.svg?style=flat-square)](https://packagist.org/packages/mimaxuz/role-manager)

## About

In many projects, you have to work with roles and permissions. Many packages are large and not all features are needed. That's why I tried to create a small but effective package for projects. This package can be attached to any project. Convenient and efficient. It is very easy to use and can be used after installation.

## Requirements

- PHP >= 8.0.2
- Laravel >= 10.0

## Installation

### Step 1: Install via Composer

```bash
composer require mimaxuz/role-manager
```

### Step 2: Run Migrations

```bash
php artisan migrate
```

### Step 3: Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=role-manager-config
```

This will create a `config/role-manager.php` file where you can customize default roles, permissions, and other settings.

### Step 4: Add Trait to User Model

Open your `App\Models\User` model and add the `HasPermissions` trait:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use MIMAXUZ\LRoles\Traits\HasPermissions;

class User extends Authenticatable
{
    use HasPermissions;

    // ...
}
```

## Database Structure

This package creates the following tables with cascade relationships:

![Database Diagram](http://yaqubov.info/packages/database_diagram_for_roles_and_permissions_laravel.jpg)

| Table | Description |
|-------|-------------|
| `x_roles` | Stores role definitions (name, slug) |
| `x_permissions` | Stores permission definitions (name, slug) |
| `roles_permissions` | Pivot table linking roles to permissions |
| `users_roles` | Pivot table linking users to roles |

## Artisan Commands

### Role Management

```bash
# Create a new role
php artisan role:create "Admin"
php artisan role:create "Super Admin" --slug=super-admin

# List all roles
php artisan role:list
php artisan role:list --with-permissions
php artisan role:list --with-users

# Assign role to user
php artisan role:assign user@example.com admin
php artisan role:assign 1 admin --by=id

# Remove role from user
php artisan role:assign user@example.com admin --remove
```

### Permission Management

```bash
# Create a new permission
php artisan permission:create "Edit Users"
php artisan permission:create "Delete Posts" --slug=delete-posts

# List all permissions
php artisan permission:list
php artisan permission:list --with-roles
```

### Attach Permissions to Roles

```bash
# Attach single permission
php artisan role:permission admin edit-users

# Attach multiple permissions
php artisan role:permission admin edit-users delete-users view-users

# Detach permissions
php artisan role:permission admin edit-users --detach

# Sync permissions (replace all existing)
php artisan role:permission admin edit-users delete-users --sync
```

### Seed Default Roles & Permissions

```bash
# Seed from config
php artisan role:seed

# Fresh seed (clear existing and reseed)
php artisan role:seed --fresh

# Seed only permissions
php artisan role:seed --permissions-only

# Seed only roles
php artisan role:seed --roles-only
```

## Configuration

After publishing the config file, you can customize:

```php
// config/role-manager.php

return [
    // User model class
    'user_model' => App\Models\User::class,

    // Field to lookup users in CLI commands
    'user_lookup_field' => 'email',

    // Default roles to seed
    'default_roles' => [
        [
            'name' => 'Administrator',
            'slug' => 'admin',
            'permissions' => ['*'], // All permissions
        ],
        [
            'name' => 'User',
            'slug' => 'user',
            'permissions' => [],
        ],
    ],

    // Default permissions to seed
    'default_permissions' => [
        ['name' => 'View Users', 'slug' => 'view-users'],
        ['name' => 'Create Users', 'slug' => 'create-users'],
        ['name' => 'Edit Users', 'slug' => 'edit-users'],
        ['name' => 'Delete Users', 'slug' => 'delete-users'],
    ],

    // Roles that have all permissions automatically
    'super_admin_roles' => ['admin'],
];
```

## Usage

### Programmatic Usage

#### Creating Roles & Permissions

```php
use MIMAXUZ\LRoles\Models\XRoles;
use MIMAXUZ\LRoles\Models\XPermissions;

// Create a role
$role = XRoles::create([
    'name' => 'Editor',
    'slug' => 'editor'
]);

// Create a permission
$permission = XPermissions::create([
    'name' => 'Edit Posts',
    'slug' => 'edit-posts'
]);

// Attach permission to role
$role->permissions()->attach($permission->id);

// Or attach multiple permissions
$role->permissions()->attach([1, 2, 3]);

// Sync permissions (replace existing)
$role->permissions()->sync([1, 2, 3]);
```

#### Assigning Roles to Users

```php
// Assign a role
$user->roles()->attach($roleId);

// Assign multiple roles
$user->roles()->attach([1, 2]);

// Remove a role
$user->roles()->detach($roleId);

// Sync roles (replace existing)
$user->roles()->sync([1, 2]);
```

#### Checking Roles & Permissions

```php
// Check if user has a role
if ($user->hasRole('admin')) {
    // User is admin
}

// Check multiple roles (OR logic)
if ($user->hasRole('admin', 'editor')) {
    // User is admin OR editor
}

// Check if user has permission
if ($user->hasPermissionTo($permission)) {
    // User has the permission
}

// Using Laravel's can() method
if ($user->can('edit-posts')) {
    // User can edit posts
}
```

### Route Middleware

Protect routes using the `role` middleware:

```php
// Single role
Route::group(['middleware' => 'role:admin'], function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});

// Role with permission check
Route::group(['middleware' => 'role:admin,delete-users'], function () {
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});
```

### Blade Directives

#### @role / @endrole

```blade
@role('admin')
    <a href="/admin">Admin Panel</a>
@endrole

@role('editor')
    <a href="/posts/create">Create Post</a>
@endrole
```

#### @hasPermission / @endhasPermission

```blade
@hasPermission('edit-users')
    <button>Edit User</button>
@endhasPermission

@hasPermission('delete-users')
    <button class="danger">Delete User</button>
@endhasPermission
```

#### @hasAnyRole / @endhasAnyRole

```blade
@hasAnyRole(['admin', 'editor'])
    <a href="/content">Manage Content</a>
@endhasAnyRole
```

#### Using Laravel's @can

```blade
@can('edit-posts')
    <button>Edit Post</button>
@endcan

@cannot('delete-posts')
    <span>You cannot delete posts</span>
@endcannot
```

## Quick Start Example

```bash
# 1. Install and migrate
composer require mimaxuz/role-manager
php artisan migrate

# 2. Publish config
php artisan vendor:publish --tag=role-manager-config

# 3. Seed default roles and permissions
php artisan role:seed

# 4. Create custom role
php artisan role:create "Content Manager" --slug=content-manager

# 5. Create custom permissions
php artisan permission:create "Publish Posts" --slug=publish-posts
php artisan permission:create "Moderate Comments" --slug=moderate-comments

# 6. Attach permissions to role
php artisan role:permission content-manager publish-posts moderate-comments

# 7. Assign role to user
php artisan role:assign john@example.com content-manager

# 8. View all roles with permissions
php artisan role:list --with-permissions
```

## Available Trait Methods

| Method | Description |
|--------|-------------|
| `hasRole(...$roles)` | Check if user has any of the given roles |
| `hasPermissionTo($permission)` | Check if user has a specific permission |
| `givePermissionsTo(...$permissions)` | Assign direct permissions to user |
| `withdrawPermissionsTo(...$permissions)` | Remove permissions from user |
| `refreshPermissions(...$permissions)` | Replace all user permissions |
| `roles()` | Get user's roles relationship |

## Changelog

### v2.0.0
- Added Artisan commands for role and permission management
- Added configuration file with default roles/permissions
- Added seeding command (`role:seed`)
- Added new Blade directives (`@hasPermission`, `@hasAnyRole`)
- Fixed bugs in HasPermissions trait
- Laravel 10, 11, 12 support

### v1.x
- Initial release with basic role/permission functionality

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Issues

If you discover any issues or bugs, please report them at [GitHub Issues](https://github.com/MIMAXUZ/role-manager/issues).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
