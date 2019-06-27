<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\SystemMediaCategory;
use Faker\Generator as Faker;

$factory->define(SystemMediaCategory::class, function (Faker $faker) {
    return [
        'parent_id' => 0,
        'name' => 'cate_'.$faker->unique()->word,
    ];
});
