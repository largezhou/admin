<?php

namespace App\Models;

use App\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

class Model extends \Illuminate\Database\Eloquent\Model
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
