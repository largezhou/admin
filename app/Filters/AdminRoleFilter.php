<?php

namespace App\Filters;

use App\Models\AdminPermission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AdminRoleFilter extends Filter
{
    protected $simpleFilters = [
        'id',
        'name' => ['like', '%?%'],
        'slug' => ['like', '%?%'],
    ];
    protected $filters = ['permission_name'];

    protected function permissionName($val)
    {
        $this->builder->whereHas('permissions', function (Builder $query) use ($val) {
            $query->where('name', 'like', "{$val}%");
        }, '>', 0);
    }
}
