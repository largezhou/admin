<?php

namespace App\Http\Middleware;

use App\Contracts\PermissionMiddleware;

class Permission extends PermissionMiddleware
{
    /**
     * @var array 白名单
     */
    protected $excepts = [
        '/auth/login',
        '/auth/logout',
        '/user',
        '/configs/vue-routers',
    ];
}
