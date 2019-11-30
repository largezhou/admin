<?php

namespace App\Admin;

use App\Admin\Middleware\AdminPermission;
use App\Admin\Utils\Admin;
use Illuminate\Support\ServiceProvider;

class AdminProvider extends ServiceProvider
{
    protected $middlewareMap = [
        'force-json' => \App\Admin\Middleware\ForceJson::class,
        'admin.permission' => \App\Admin\Contracts\PermissionMiddleware::class,
        'admin.auth' => \App\Admin\Middleware\Authenticate::class,
    ];
    protected $middlewareGroups = [
        'admin' => [
            'force-json',
            'bindings',
        ],
    ];
    protected $commands = [
        Console\Commands\AdminInitCommand::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(Contracts\PermissionMiddleware::class, function () {
            return new AdminPermission();
        });

        foreach ($this->middlewareMap as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }

        $this->loadRoutesFrom(Admin::path('routes.php'));
    }
}
