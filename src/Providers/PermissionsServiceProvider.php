<?php

namespace MIMAXUZ\LRoles\Providers;

use MIMAXUZ\LRoles\Models\XPermissions;
use MIMAXUZ\LRoles\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Routing\Router;

class PermissionsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
      //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
         $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');

         // if ($this->app->runningInConsole()) {
        //     $this->commands([
        //         Commands\PermissionsGenerate::class,
        //         Commands\PermissionsClear::class,
        //     ]);
        // }
        $router->aliasMiddleware('role', RoleMiddleware::class);

        try {
            XPermissions::get()->map(function ($permission) {
                Gate::define($permission->slug, function ($user) use ($permission) {
                    return $user->hasPermissionTo($permission);
                });
            });
        } catch (\Exception $e) {
            report($e);
            return false;
        }
        //Blade directives
        Blade::directive('role', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
        });
        Blade::directive('endrole', function ($role) {
            return "<?php endif ?>";
        });
    }
}
