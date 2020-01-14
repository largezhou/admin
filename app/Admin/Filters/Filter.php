<?php

namespace App\Admin\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class Filter
{
    protected $request;

    /**
     * @var Builder
     */
    protected $builder;

    protected $filters = [];

    protected $simpleFilters = [];

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->formatSimpleFilters();
    }

    /**
     * 把 simpleFilters 中没有指定过滤类型的, 自动改为 '='
     */
    protected function formatSimpleFilters()
    {
        $t = [];
        foreach ($this->simpleFilters as $field => $op) {
            if (is_int($field)) {
                $t[$op] = 'equal';
            } else {
                $t[$field] = $op;
            }
        }
        $this->simpleFilters = $t;
    }

    /**
     * 应用过滤
     *
     * @param $builder
     *
     * @return mixed
     */
    public function apply($builder)
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter => $value) {
            if (is_null($value)) {
                continue;
            }

            if ($op = $this->simpleFilters[$filter] ?? null) { // 简单过滤应用
                $this->applySimpleFilter($filter, $op, $value);
            } else { // 其他自定义过滤
                $method = Str::camel($filter);
                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
            }
        }

        return $this->builder;
    }

    /**
     * 过滤简单过滤器
     *
     * @param string $filter 过滤字段
     * @param string|array $op 操作
     * @param mixed $value 请求中对应的值
     */
    protected function applySimpleFilter($filter, $op, $value)
    {
        if (is_array($op)) {
            $args = array_slice($op, 1);
            $op = $op[0];
        }

        switch ($op) {
            case 'equal':
                $this->builder->where($filter, $value);
                break;
            case 'like':
                $this->builder->where($filter, 'like', str_replace('?', $value, $args[0]));
                break;
            case 'in':
                if (is_string($value)) {
                    $value = explode(',', $value);
                }
                $this->builder->whereIn($filter, $value);
                break;
            default:
                // do nothing
        }
    }

    public function getFilters()
    {
        return $this->request->only(array_merge($this->filters, array_keys($this->simpleFilters)));
    }

    /**
     * 只保留特定的过滤字段
     *
     * @param array|string $only
     *
     * @return $this
     */
    public function only($only)
    {
        if (is_string($only)) {
            $only = [$only];
        }

        $this->filters = array_intersect($this->filters, $only);
        $this->simpleFilters = array_intersect_key($this->simpleFilters, array_flip($only));

        return $this;
    }
}
