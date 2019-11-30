<?php

namespace App\Admin\Filters;

class ConfigFilter extends Filter
{
    protected $simpleFilters = [
        'category_id',
        'name' => ['like', '%?%'],
        'slug' => ['like', '%?%'],
    ];
}
