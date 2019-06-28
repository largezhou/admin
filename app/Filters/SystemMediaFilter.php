<?php

namespace App\Filters;

class SystemMediaFilter extends Filter
{
    protected $simpleFilters = [
        'ext' => 'in',
        'filename' => ['like', '%?%'],
    ];
}
