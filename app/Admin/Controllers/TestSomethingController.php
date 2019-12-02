<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Config;

class TestSomethingController extends Controller
{
    public function index($path = null)
    {
        $inserts = factory(Config::class)->make(['category_id' => 1, 'options' => 'options'])->toArray();
        dd(Config::create($inserts));
    }
}
