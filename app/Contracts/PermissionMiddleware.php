<?php
/**
 * 来自 laravel-admin
 */

namespace App\Contracts;

use App\Models\AdminPermission;
use App\Utils\Admin;
use App\Utils\PermissionChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class PermissionMiddleware
{
    /**
     * @var string
     */
    protected $middlewarePrefix = 'admin.permission:';
    /**
     * @var array 白名单
     */
    protected $excepts = [];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param array $args
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, ...$args)
    {
        if (!Admin::user()) {
            PermissionChecker::error();
        }

        if (!empty($args) || $this->shouldPassThrough($request)) {
            return $next($request);
        }

        if ($this->checkRoutePermission($request)) {
            return $next($request);
        }

        if (!Admin::user()->allPermissions()->first(function (AdminPermission $permission) use ($request) {
            return PermissionChecker::shouldPassThrough($permission, $request);
        })) {
            PermissionChecker::error();
        }

        return $next($request);
    }

    /**
     * 如果路由的中间件组中, 有以 'admin.permission:' 开头的, 说明是单独设置了权限, 要优先处理
     *
     * @param Request $request
     *
     * @return bool
     */
    public function checkRoutePermission(Request $request)
    {
        if (!$middleware = collect($request->route()->middleware())->first(function ($middleware) {
            return Str::startsWith($middleware, $this->middlewarePrefix);
        })) {
            return false;
        }

        $args = explode(',', str_replace($this->middlewarePrefix, '', $middleware));

        $method = array_shift($args);

        if (!method_exists(PermissionChecker::class, $method)) {
            throw new \InvalidArgumentException("无效的权限检测方法 [ $method ]");
        }

        call_user_func_array([PermissionChecker::class, $method], [$args]);

        return true;
    }

    /**
     * 白名单检测
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        return collect($this->excepts)
            ->contains(function ($except) use ($request) {
                $except = Admin::url($except);
                return $request->is($except);
            });
    }
}
