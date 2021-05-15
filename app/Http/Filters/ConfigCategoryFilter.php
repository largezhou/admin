<?php

namespace App\Http\Filters;

class ConfigCategoryFilter extends Filter
{
    protected $simpleFilters = [
        'name' => ['like', '%?%'],
        'slug' => ['like', '%?%'],
    ];
}
