<?php

namespace Tests;

use App\Contracts\PermissionMiddleware;
use App\Http\Middleware\AdminPermission;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

abstract class AdminTestCase extends TestCase
{
    protected $routePrefix = 'admin';
    /**
     * @var AdminUser
     */
    protected $user;
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem|\Illuminate\Contracts\Filesystem\Cloud
     */
    protected $storage;
    protected $filesystem = 'uploads';

    protected function login(AdminUser $user = null)
    {
        $user = $user ?: factory(AdminUser::class)->create(['username' => 'admin']);
        $this->actingAs($user, 'admin');

        $auth = auth('admin');
        $this->user = $user;
        $this->token = $auth->tokenById($user->id);
        $auth->setToken($this->token);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->checkPermission(false);

        $this->storage = Storage::disk($this->filesystem);
    }

    /**
     * 设置是否需要检测权限
     *
     * @param bool $check
     */
    protected function checkPermission($check)
    {
        if ($check) {
            $ins = new class extends AdminPermission
            {
                protected $urlWhitelist = [
                    '/test-resources/pass-through',
                ];
            };
        } else {
            $ins = new class extends AdminPermission
            {
                public function handle(Request $request, \Closure $next, ...$args)
                {
                    return $next($request);
                }
            };
        }

        $this->app->singleton(PermissionMiddleware::class, function () use ($ins) {
            return $ins;
        });
    }
}
