<?php

namespace App\Admin\Filters\Traits;

use Illuminate\Database\Eloquent\Builder;

trait RolePermissionFilter
{
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
