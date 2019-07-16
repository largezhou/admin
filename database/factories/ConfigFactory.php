<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Config;
use Faker\Generator as Faker;

$factory->define(Config::class, function (Faker $faker) {
    return [
        'category_id' => 0,
        'type' => $faker->randomElement(array_keys(Config::$typeMap)),
        'name' => 'name_'.$faker->unique()->word,
        'slug' => 'slug_'.$faker->unique()->word,
        'desc' => (mt_rand(0, 9) > 8) ? ('desc '.$faker->paragraph) : null,
        'options' => null,
        'value' => null,
    ];
});
