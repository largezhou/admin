<?php

namespace App\Admin\Filters;

class SystemMediaFilter extends Filter
{
    protected $simpleFilters = [
        'category_id',
        'ext' => 'in',
        'filename' => ['like', '%?%'],
    ];
}
