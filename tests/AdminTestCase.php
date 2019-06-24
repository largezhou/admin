<?php

namespace Tests;

use App\Contracts\PermissionMiddleware;
use App\Http\Middleware\Permission;
use App\Models\AdminUser;
use Illuminate\Http\Request;

class AdminTestCase extends TestCase
{
    protected $routePrefix = 'admin';
    /**
     * @var AdminUser
     */
    protected $user;

    protected function login(AdminUser $user = null)
    {
        $user = $user ?: factory(AdminUser::class)->create(['username' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $auth = auth('admin-api');
        $this->user = $user;
        $this->token = $auth->tokenById($user->id);
        $auth->setToken($this->token);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->checkPermission(false);
    }

    /**
     * 设置是否需要检测权限
     *
     * @param bool $check
     */
    protected function checkPermission($check)
    {
        if ($check) {
            $ins = new class extends Permission
            {
            };
        } else {
            $ins = new class extends Permission
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
