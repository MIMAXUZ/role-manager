# Simple Role Manager
An easy and flexible Laravel authorization and roles permission management

[![Total Downloads](https://img.shields.io/packagist/dt/mimaxuz/role-manager.svg?style=flat-square)](https://packagist.org/packages/mimaxuz/role-manager)

## About
In many projects, you have to work with roles and permissions. Many packages are large and not all features are needed. That’s why I tried to create a small but effective package for projects. This package can be attached to any project. Convenient and efficient. It is very easy to use and can be used after installation.

## Installation
However, if you are not using Homestead, you will need to make sure your server meets the following requirements:
- PHP >= 7.2.5
- BCMath PHP Extension
- Ctype PHP Extension
- Fileinfo PHP extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

### Install package via ```composer```
```
$ composer require mimaxuz/role-manager
```
After that, you need to run the migration files:
```
$ php artisan migrate
```
## How to use package migrations
This package includes the following tables. All tables have a relational line, and the relationships are in cascade.

![Database Diagram For Laravel Role Manager packages](http://yaqubov.info/packages/database_diagram_for_roles_and_permissions_laravel.jpg)
## How to activate package ?
This package contains traits and must be activated within the ```App\Users``` model. To do this, follow the steps below.
1. Open ```App\User``` Model and Copy the following codes
```
//For importing traits form package
Use MIMAXUZ\LRoles\Traits\HasPermissions;
...

class User extends Authenticatable
{
    use Notifiable;
    //Import The Trait
    use HasPermissions; 
    ...
 }
```

### Assign a route to a role
Besides middleware and other route settings, you can use a `role` key in your route groups to assign a role to your routes.
<br>
You can use route groups as follows.
```

Route::group(['middleware' => 'role:admin'], function () {
    //With controller
    Route::get('/a', 'HomeController@dashboard');
    //Inside functions
    Route::get('/admin', function () {
        return 'Welcome Admin';
    });
});
```
### How to use roles inside `Blade` ?
If you want to use it in `Blade Templates`, use the following.
```
 @role('admin')
   Only Admin roles user can access to it
 @endrole
 ```
 

### Conclusion
This package may have issuses and bugs. Don't forget to report if an error or problem occurs!

### License
Standt MIT License
