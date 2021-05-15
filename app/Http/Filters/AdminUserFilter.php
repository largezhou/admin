<?php

namespace App\Http\Filters;

use App\Http\Filters\Traits\RolePermissionFilter;

class AdminUserFilter extends Filter
{
    use RolePermissionFilter;
    protected $simpleFilters = [
        'id',
        'username' => ['like', '%?%'],
        'name' => ['like', '%?%'],
    ];
    protected $filters = [
        'role_name',
        'permission_name',
    ];
}
