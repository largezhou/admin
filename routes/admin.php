<?php

use Illuminate\Support\Facades\Route;

if (config('app.env') == 'local') {
    Route::any('/test', 'TestSomethingController@index');
    Route::any('/{path}/test', 'TestSomethingController@index')->where('path', '.*');
}

Route::get('/admin/{path?}', 'RedirectController@index')->where('path', '.*')->name('redirect.index');
Route::get('/admin-dev/{path?}', 'RedirectController@indexDev')->where('path', '.*')->name('redirect.index-dev');

Route::prefix('admin-api')
    ->middleware('admin')
    ->as('admin.')
    ->group(function () {
        Route::post('auth/login', 'Auth\LoginController@login')->name('login');

        Route::middleware([
            'admin.auth',
            'admin.permission',
        ])->group(function () {
            Route::post('auth/logout', 'Auth\LoginController@logout')->name('logout');

            Route::get('user', 'AdminUserController@user')->name('user');
            Route::get('user/edit', 'AdminUserController@editUser')->name('user.edit');
            Route::put('user', 'AdminUserController@updateUser')->name('user.update');

            Route::resource('admin-users', 'AdminUserController');

            Route::post('vue-routers/by-import', 'VueRouterController@importVueRouters')->name('vue-routers.by-import');
            Route::put('vue-routers', 'VueRouterController@batchUpdate')->name('vue-routers.batch.update');
            Route::resource('vue-routers', 'VueRouterController')->except(['show']);

            Route::resource('admin-permissions', 'AdminPermissionController')->except(['show']);
            Route::resource('admin-roles', 'AdminRoleController')->except(['show']);

            Route::resource('config-categories', 'ConfigCategoryController')->except(['show', 'create']);

            Route::get('configs/vue-routers', 'ConfigController@vueRouters')->name('configs.vue-routers');
            Route::put('configs/values', 'ConfigController@updateValues')->name('configs.update-values');
            Route::resource('configs', 'ConfigController')->except(['show']);
            Route::get('configs/{category_slug}', 'ConfigController@getByCategorySlug')->name('configs.by-category-slug');
            Route::get('configs/{category_slug}/values', 'ConfigController@getValuesByCategorySlug')
                ->name('configs.values.by-category-slug');

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
            Route::put('system-media', 'SystemMediaController@batchUpdate')->name('system-media.batch.update');
            Route::delete('system-media', 'SystemMediaController@batchDestroy')->name('system-media.batch.destroy');
        });
    });
