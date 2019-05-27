<?php

namespace App\Providers;

use Illuminate\Http\JsonResponse;
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

        JsonResponse::macro('withComponent', function ($name) {
            $this->vueComponent = $name;
            return $this;
        });
    }
}
