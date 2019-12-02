<?php
/**
 * 来自 laravel-admin
 */

namespace App\Admin\Utils;

use App\Admin\Models\AdminPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionChecker
{
    /**
     * 允许特定权限通过
     *
     * @param $permission
     *
     * @return true
     */
    public static function check($permission)
    {
        if (Admin::isAdministrator()) {
            return true;
        }

        if (is_array($permission)) {
            collect($permission)->each(function ($permission) {
                static::check($permission);
            });
        } elseif (Admin::user()->can($permission)) {
            return true;
        } else {
            static::error();
        }
    }

    /**
     * 允许 $roles 中的任意一个角色访问
     *
     * @param $roles
     *
     * @return true
     */
    public static function allow($roles)
    {
        if (Admin::isAdministrator()) {
            return true;
        }

        if (!Admin::user()->inRoles($roles)) {
            static::error();
        }

        return true;
    }

    /**
     * 通行
     *
     * @return bool
     */
    public static function free()
    {
        return true;
    }

    /**
     * 拒绝 roles 中的任意一个角色访问
     *
     * @param $roles
     *
     * @return true
     */
    public static function deny($roles)
    {
        if (Admin::isAdministrator()) {
            return true;
        }

        if (Admin::user()->inRoles($roles)) {
            static::error();
        }

        return true;
    }

    /**
     * 403 响应
     */
    public static function error()
    {
        abort(403, '无权访问');
    }

    /**
     * 请求路径和方法的权限检测
     *
     * @param AdminPermission $permission
     * @param Request $request
     *
     * @return bool
     */
    public static function shouldPassThrough(AdminPermission $permission, Request $request)
    {
        if (empty($permission->http_method) && empty($permission->http_path)) {
            return true;
        }

        $method = $permission->http_method;

        $matches = array_map(function ($path) use ($method) {
            if (Str::contains($path, ':')) {
                list($method, $path) = explode(':', $path);
                $method = explode(',', $method);
            }

            $path = 'admin-api'.$path;

            return compact('method', 'path');
        }, $permission->http_path);

        foreach ($matches as $match) {
            if (static::matchRequest($match, $request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检测请求的方法和路径是否匹配特定值
     *
     * @param array $match
     * @param Request $request
     *
     * @return bool
     */
    protected static function matchRequest(array $match, Request $request)
    {
        if (!$request->is(trim($match['path'], '/'))) {
            return false;
        }

        $method = collect($match['method'])->filter()->map(function ($method) {
            return strtoupper($method);
        });

        return $method->isEmpty() || $method->contains($request->method());
    }
}
