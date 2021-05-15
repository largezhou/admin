<?php

use App\Http\Controllers\Admin as C;
use Illuminate\Support\Facades\Route;

if (config('app.env') == 'local') {
    Route::any('/test', [C\TestSomethingController::class, 'index']);
    Route::any('/{path}/test', [C\TestSomethingController::class, 'index'])->where('path', '.*');
}

Route::get('/admin/{path?}', [C\RedirectController::class, 'index'])->where('path', '.*')->name('redirect.index');
Route::get('/admin-dev/{path?}', [C\RedirectController::class, 'indexDev'])->where('path', '.*')->name('redirect.index-dev');

Route::prefix('admin-api')
    ->middleware('admin')
    ->as('admin.')
    ->group(function () {
        Route::post('auth/login', [C\Auth\LoginController::class, 'login'])->name('login');

        Route::middleware([
            'admin.auth',
            'admin.permission',
        ])->group(function () {
            Route::post('auth/logout', [C\Auth\LoginController::class, 'logout'])->name('logout');

            Route::get('user', [C\AdminUserController::class, 'user'])->name('user');
            Route::get('user/edit', [C\AdminUserController::class, 'editUser'])->name('user.edit');
            Route::put('user', [C\AdminUserController::class, 'updateUser'])->name('user.update');

            Route::resource('admin-users', C\AdminUserController::class);

            Route::post('vue-routers/by-import', [C\VueRouterController::class, 'importVueRouters'])->name('vue-routers.by-import');
            Route::put('vue-routers', [C\VueRouterController::class, 'batchUpdate'])->name('vue-routers.batch.update');
            Route::resource('vue-routers', C\VueRouterController::class)->except(['show']);

            Route::resource('admin-permissions', C\AdminPermissionController::class)->except(['show']);
            Route::resource('admin-roles', C\AdminRoleController::class)->except(['show']);

            Route::resource('config-categories', C\ConfigCategoryController::class)->except(['show', 'create']);

            // 清除并缓存配置
            Route::post('configs/cache', [C\ConfigController::class, 'cache'])->name('configs.cache');
            // 获取后台路由配置，会做权限筛选，用来生成前端菜单和路由
            Route::get('configs/vue-routers', [C\ConfigController::class, 'vueRouters'])->name('configs.vue-routers');
            // 配置的增删改差
            Route::resource('configs', C\ConfigController::class)->except(['show']);
            // 通过分类名获取配置列表，用在用户在后台配置时，生成配置表单
            Route::get('configs/{category_slug}', [C\ConfigController::class, 'getByCategorySlug'])->name('configs.by-category-slug');
            // 通过分类名获取配置的键值对，用在前端获取配置键值对，比如系统首次请求，获取后台名称，是否需要验证码等配置
            Route::get('configs/{category_slug}/values', [C\ConfigController::class, 'getValuesByCategorySlug'])
                ->name('configs.values.by-category-slug');
            // 用户在后台更新配置
            Route::put('configs/{category_slug}/values', [C\ConfigController::class, 'updateValues'])
                ->name('configs.update-values');

            Route::resource('system-media-categories', C\SystemMediaCategoryController::class)->except(['show', 'create']);
            // 在指定分类下，上传文件
            Route::post(
                'system-media-categories/{system_media_category}/system-media',
                [C\SystemMediaCategoryController::class, 'storeSystemMedia']
            )->name('system-media-categories.system-media.store');
            // 获取指定分类下的所有文件
            Route::get(
                'system-media-categories/{system_media_category}/system-media',
                [C\SystemMediaCategoryController::class, 'systemMediaIndex']
            )->name('system-media-categories.system-media.index');

            Route::resource('system-media', C\SystemMediaController::class)
                // system-media 自动转成单数后 变为了 system-sedium
                // 所以手动指定
                ->parameters(['system-media' => 'system_media'])
                ->except(['store', 'show', 'create']);
            Route::put('system-media', [C\SystemMediaController::class, 'batchUpdate'])->name('system-media.batch.update');
            Route::delete('system-media', [C\SystemMediaController::class, 'batchDestroy'])->name('system-media.batch.destroy');
        });
    });
