<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:admin_api')
    ->group(function () {
        Route::post('/auth/logout', 'Auth\LoginController@logout')->name('logout');

        Route::get('/user', 'AdminUserController@user')->name('user');
    });

Route::post('/auth/login', 'Auth\LoginController@login')->name('login');
