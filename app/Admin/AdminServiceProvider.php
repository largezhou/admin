<?php

namespace App\Admin;

use App\Admin\Models\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    protected $middlewareMap = [
        'admin.permission' => \App\Admin\Middleware\AdminPermission::class,
        'admin.auth' => \App\Admin\Middleware\Authenticate::class,
    ];

    protected $middlewareGroups = [
        'admin' => [
            \App\Admin\Middleware\ForceJson::class,
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    protected $commands = [
        Console\Commands\AdminInitCommand::class,
        Console\Commands\ResourceMakeCommand::class,
        Console\Commands\CacheConfig::class,
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
        Config::loadToConfig();

        foreach ($this->middlewareMap as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }

        Route::group([], base_path('routes/admin.php'));
    }
}
