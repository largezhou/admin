<?php

namespace App\Providers;

use App\Models\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    protected $middlewareMap = [
        'admin.permission' => \App\Http\Middleware\AdminPermission::class,
        'admin.auth' => \App\Http\Middleware\AdminAuthenticate::class,
    ];

    protected $middlewareGroups = [
        'admin' => [
            \App\Http\Middleware\ForceJson::class,
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    protected $commands = [
        \App\Console\Commands\AdminInitCommand::class,
        \App\Console\Commands\ResourceMakeCommand::class,
        \App\Console\Commands\CacheConfig::class,
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
