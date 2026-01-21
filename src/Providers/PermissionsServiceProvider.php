<?php

namespace MIMAXUZ\LRoles\Providers;

use MIMAXUZ\LRoles\Commands\AssignRoleCommand;
use MIMAXUZ\LRoles\Commands\AttachPermissionCommand;
use MIMAXUZ\LRoles\Commands\CreatePermissionCommand;
use MIMAXUZ\LRoles\Commands\CreateRoleCommand;
use MIMAXUZ\LRoles\Commands\ListPermissionsCommand;
use MIMAXUZ\LRoles\Commands\ListRolesCommand;
use MIMAXUZ\LRoles\Commands\SeedDefaultsCommand;
use MIMAXUZ\LRoles\Middleware\RoleMiddleware;
use MIMAXUZ\LRoles\Models\XPermissions;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PermissionsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/role-manager.php',
            'role-manager'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/role-manager.php' => config_path('role-manager.php'),
        ], 'role-manager-config');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateRoleCommand::class,
                CreatePermissionCommand::class,
                AssignRoleCommand::class,
                AttachPermissionCommand::class,
                SeedDefaultsCommand::class,
                ListRolesCommand::class,
                ListPermissionsCommand::class,
            ]);
        }

        // Register middleware
        $router->aliasMiddleware('role', RoleMiddleware::class);

        // Register Gates
        $this->registerGates();

        // Register Blade directives
        $this->registerBladeDirectives();
    }

    /**
     * Register permission gates.
     *
     * @return void
     */
    protected function registerGates(): void
    {
        try {
            XPermissions::get()->each(function ($permission) {
                Gate::define($permission->slug, function ($user) use ($permission) {
                    return $user->hasPermissionTo($permission);
                });
            });
        } catch (\Exception $e) {
            // Table might not exist yet during migration
            report($e);
        }
    }

    /**
     * Register Blade directives.
     *
     * @return void
     */
    protected function registerBladeDirectives(): void
    {
        // Check if user has a specific role
        Blade::directive('role', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });

        // Check if user has a specific permission
        Blade::directive('hasPermission', function ($permission) {
            return "<?php if(auth()->check() && auth()->user()->can({$permission})): ?>";
        });

        Blade::directive('endhasPermission', function () {
            return "<?php endif; ?>";
        });

        // Check if user has any of the given roles
        Blade::directive('hasAnyRole', function ($roles) {
            return "<?php if(auth()->check() && auth()->user()->hasRole(...(array){$roles})): ?>";
        });

        Blade::directive('endhasAnyRole', function () {
            return "<?php endif; ?>";
        });
    }
}
