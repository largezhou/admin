<?php

namespace App\Admin\Traits;

use App\Admin\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

/**
 * 通用模型中的方法
 *
 * Trait ModelHelpers
 * @package App\Admin\Traits
 */
trait ModelHelpers
{
    /**
     * 最大每页数，避免瞎搞的人
     *
     * @var int
     */
    protected $maxPerPage = 200;

    public function getPerPage()
    {
        $perPage = Request::get('per_page');
        $intPerPage = (int) $perPage;
        if (($intPerPage > 0) && ((string) $intPerPage === $perPage)) {
            return min($intPerPage, $this->maxPerPage);
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
