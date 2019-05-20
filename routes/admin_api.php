<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:admin_api')
    ->group(function () {
        Route::get('/index', function () {
            return 'index';
        })->name('index');
    });

Route::post('/auth/login', 'Auth\LoginController@login')->name('login');
