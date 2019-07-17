<?php

namespace App\Filters;

use App\Models\ConfigCategory;

class ConfigFilter extends Filter
{
    protected $simpleFilters = [
        'category_id',
        'name' => ['like', '%?%'],
        'slug' => ['like', '%?%'],
        'type',
    ];
    protected $filters = [
        'category_name',
    ];

    /**
     * 分类名称模糊筛选
     *
     * @param $value
     */
    protected function categoryName($value)
    {
        $cateIds = ConfigCategory::query()
            ->where('name', 'like', "%{$value}%")
            ->pluck('id');
        if ($cateIds->isNotEmpty()) {
            $this->builder->whereIn('category_id', $cateIds);
        }
    }
}
