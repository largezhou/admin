<?php

namespace App\Http\Middleware;

use App\Contracts\PermissionMiddleware;

class AdminPermission extends PermissionMiddleware
{
    protected $urlWhitelist = [
        '/auth/login',
        '/auth/logout',
        '/user',
        '/user/edit',
        '/configs/vue-routers',
        '/configs/system_basic/values',
    ];
}
