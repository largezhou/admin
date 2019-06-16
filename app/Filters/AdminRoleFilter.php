<?php

namespace App\Filters;

use App\Models\AdminPermission;
use Illuminate\Support\Facades\DB;

class AdminRoleFilter extends Filter
{
    protected $simpleFilters = [
        'id',
        'name' => ['like', '?%'],
        'slug' => ['like', '?%'],
    ];
    protected $filters = ['permission_name'];

    protected function permissionName($value)
    {
        $perms = AdminPermission::query()->where('name', 'like', $value.'%')->pluck('id');
        if ($perms->isEmpty()) {
            return;
        }
        $roleIds = DB::table('admin_permission_role')->whereIn('permission_id', $perms)->pluck('role_id');
        if ($roleIds->isEmpty()) {
            return;
        }
        $this->builder->whereIn('id', $roleIds);
    }
}
