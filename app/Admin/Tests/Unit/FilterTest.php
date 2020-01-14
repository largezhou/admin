<?php

namespace App\Admin\Tests\Unit;

use App\Admin\Filters\Filter;
use App\Admin\Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;

class FilterTest extends AdminTestCase
{
    public function testOnly()
    {
        $samples = [
            ['id', 'custom'],
            ['id', 'other', 'custom'],
            ['custom', 'other'],
            'custom',
            'other',
        ];

        foreach ($samples as $s) {
            $filter = $this->app->make(MockFilter::class);
            $filter->only($s);

            $allFilters = array_merge($filter->filters, array_keys($filter->simpleFilters));

            $this->assertTrue(empty(array_diff($allFilters, Arr::wrap($s))));
            $this->assertTrue(count($allFilters) == count(Arr::wrap($s)));
        }
    }
}

class MockFilter extends Filter
{
    public $simpleFilters = [
        'id',
        'other' => ['like', '%?%'],
    ];

    public $filters = [
        'custom',
    ];
}
