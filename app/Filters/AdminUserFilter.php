<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class AdminUserFilter extends Filter
{
    protected $simpleFilters = [
        'id',
        'username' => ['like', '%?%'],
        'name' => ['like', '%?%'],
    ];
    protected $filters = [
        'role_name',
        'permission_name',
    ];

    protected function roleName($val)
    {
        $this->builder->whereHas('roles', function (Builder $query) use ($val) {
            $query->where('name', 'like', "%{$val}%");
        }, '>', 0);
    }

    protected function permissionName($val)
    {
        $this->builder->whereHas('permissions', function (Builder $query) use ($val) {
            $query->where('name', 'like', "%{$val}%");
        }, '>', 0);
    }
}
