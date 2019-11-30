<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Admin\Models\ConfigCategory;
use Faker\Generator as Faker;

$factory->define(ConfigCategory::class, function (Faker $faker) {
    return [
        'name' => 'cate_'.$faker->unique()->word,
        'slug' => 'slug_'.$faker->unique()->word,
    ];
});
