<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | Specify the user model class that has the HasPermissions trait.
    | This is used when assigning roles to users via CLI commands.
    |
    */

    'user_model' => App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | User Lookup Field
    |--------------------------------------------------------------------------
    |
    | The field used to look up users when assigning roles via CLI.
    | Supported: 'id', 'email', or any unique field on your user model.
    |
    */

    'user_lookup_field' => 'email',

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    |
    | Customize the table names if needed. These should match your migration.
    |
    */

    'tables' => [
        'roles' => 'x_roles',
        'permissions' => 'x_permissions',
        'role_permissions' => 'roles_permissions',
        'user_roles' => 'users_roles',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Roles
    |--------------------------------------------------------------------------
    |
    | Define default roles that should be seeded when running `role:seed`.
    | Each role can have a name, slug, and an array of permission slugs.
    | Use ['*'] for permissions to assign all permissions to a role.
    |
    */

    'default_roles' => [
        [
            'name' => 'Administrator',
            'slug' => 'admin',
            'permissions' => ['*'], // All permissions
        ],
        [
            'name' => 'Moderator',
            'slug' => 'moderator',
            'permissions' => [
                'view-users',
                'edit-users',
            ],
        ],
        [
            'name' => 'User',
            'slug' => 'user',
            'permissions' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Permissions
    |--------------------------------------------------------------------------
    |
    | Define default permissions that should be seeded when running `role:seed`.
    | Format: ['name' => 'Human Readable Name', 'slug' => 'slug-name']
    | Or simply: 'slug-name' (name will be auto-generated from slug)
    |
    */

    'default_permissions' => [
        // User management
        ['name' => 'View Users', 'slug' => 'view-users'],
        ['name' => 'Create Users', 'slug' => 'create-users'],
        ['name' => 'Edit Users', 'slug' => 'edit-users'],
        ['name' => 'Delete Users', 'slug' => 'delete-users'],

        // Role management
        ['name' => 'View Roles', 'slug' => 'view-roles'],
        ['name' => 'Create Roles', 'slug' => 'create-roles'],
        ['name' => 'Edit Roles', 'slug' => 'edit-roles'],
        ['name' => 'Delete Roles', 'slug' => 'delete-roles'],

        // Permission management
        ['name' => 'View Permissions', 'slug' => 'view-permissions'],
        ['name' => 'Create Permissions', 'slug' => 'create-permissions'],
        ['name' => 'Edit Permissions', 'slug' => 'edit-permissions'],
        ['name' => 'Delete Permissions', 'slug' => 'delete-permissions'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Super Admin Roles
    |--------------------------------------------------------------------------
    |
    | Define which role slug(s) should be considered "super admin" and
    | automatically have all permissions without explicit assignment.
    |
    */

    'super_admin_roles' => ['admin'],

];
