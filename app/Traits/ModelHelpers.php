<?php

namespace App\Traits;

use App\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

/**
 * 通用模型中的方法
 *
 * Trait ModelHelpers
 * @package App\Traits
 */
trait ModelHelpers
{
    public function getPerPage()
    {
        $perPage = Request::get('per_page');
        $int = (int) $perPage;
        if (($int > 0) && ((string) $int === $perPage)) {
            return $int;
        } else {
            return $this->perPage;
        }
    }

    /**
     * 应用过滤器
     *
     * @param Builder $builder
     * @param Filter $filter
     *
     * @return mixed
     */
    public function scopeFilter(Builder $builder, Filter $filter)
    {
        return $filter->apply($builder);
    }
}
