<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:admin',
    'admin.permission',
])->group(function () {
    Route::post('auth/logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('user', 'AdminUserController@user')->name('user');
    Route::get('user/edit', 'AdminUserController@editUser')->name('user.edit');
    Route::put('user', 'AdminUserController@updateUser')->name('user.update');

    Route::resource('admin-users', 'AdminUserController');
    Route::resource('vue-routers', 'VueRouterController')->except(['show', 'create']);
    Route::resource('admin-permissions', 'AdminPermissionController')->except(['show']);
    Route::resource('admin-roles', 'AdminRoleController')->except(['show']);

    Route::prefix('configs')
        ->as('configs.')
        ->group(function () {
            Route::get('vue-routers', 'ConfigController@vueRouters')->name('vue-routers');
        });

    Route::resource('system-media-categories', 'SystemMediaCategoryController')->except(['show', 'create']);
    // 在指定分类下，上传文件
    Route::post(
        'system-media-categories/{system_media_category}/system-media',
        'SystemMediaCategoryController@storeSystemMedia'
    )->name('system-media-categories.system-media.store');
    // 获取指定分类下的所有文件
    Route::get(
        'system-media-categories/{system_media_category}/system-media',
        'SystemMediaCategoryController@systemMediaIndex'
    )->name('system-media-categories.system-media.index');

    Route::resource('system-media', 'SystemMediaController')
        // system-media 自动转成单数后 变为了 system-sedium
        // 所以手动指定
        ->parameters(['system-media' => 'system_media'])
        ->except(['store', 'show', 'create']);
});

Route::post('auth/login', 'Auth\LoginController@login')->name('login');

Route::post('/demo/reset-system', 'DemoController@resetSystem');
