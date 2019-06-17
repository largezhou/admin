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
        $permIds = AdminPermission::query()->where('name', 'like', $value.'%')->pluck('id');
        if ($permIds->isEmpty()) {
            $roleIds = [];
        } else {
            $roleIds = DB::table('admin_role_permission')->whereIn('permission_id', $permIds)->pluck('role_id');
        }
        $this->builder->whereIn('id', $roleIds);
    }
}
