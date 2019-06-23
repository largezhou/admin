<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:admin-api')
    ->group(function () {
        Route::post('/auth/logout', 'Auth\LoginController@logout')->name('logout');

        Route::get('/user', 'AdminUserController@user')->name('user');
        Route::resource('/admin-users', 'AdminUserController');
        Route::resource('/vue-routers', 'VueRouterController')->except(['show', 'create']);
        Route::resource('/admin-permissions', 'AdminPermissionController')->except(['show']);
        Route::resource('/admin-roles', 'AdminRoleController')->except(['show']);

        Route::prefix('configs')
            ->as('configs.')
            ->group(function () {
                Route::get('vue-routers', 'ConfigController@vueRouters')->name('vue-routers');
            });
    });

Route::post('/auth/login', 'Auth\LoginController@login')->name('login');
