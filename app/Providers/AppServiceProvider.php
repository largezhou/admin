<?php

namespace App\Providers;

use App\Contracts\PermissionMiddleware;
use App\Http\Middleware\Permission;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Resource::withoutWrapping();

        $this->app->singleton(PermissionMiddleware::class, function () {
            return new Permission();
        });
    }
}
