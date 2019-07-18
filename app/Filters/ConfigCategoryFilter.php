<?php

namespace App\Filters;

class ConfigCategoryFilter extends Filter
{
    protected $simpleFilters = [
        'name' => ['like', '%?%'],
    ];
}
