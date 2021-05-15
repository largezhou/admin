<?php

namespace App\Http\Filters;

class SystemMediaFilter extends Filter
{
    protected $simpleFilters = [
        'category_id',
        'ext' => 'in',
        'filename' => ['like', '%?%'],
    ];
}
