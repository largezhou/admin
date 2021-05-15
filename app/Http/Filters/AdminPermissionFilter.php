<?php

namespace App\Http\Filters;

class AdminPermissionFilter extends Filter
{
    protected $simpleFilters = [
        'id',
        'slug' => ['like', '%?%'],
        'name' => ['like', '%?%'],
        'http_path' => ['like', '%?%'],
    ];
}
