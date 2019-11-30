<?php

namespace App\Admin\Filters;

class ConfigCategoryFilter extends Filter
{
    protected $simpleFilters = [
        'name' => ['like', '%?%'],
        'slug' => ['like', '%?%'],
    ];
}
