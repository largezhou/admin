<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Admin\Models\AdminRole;
use Faker\Generator as Faker;

$factory->define(AdminRole::class, function (Faker $faker) {
    return [
        'name' => 'role_'.$faker->unique()->word,
        'slug' => $faker->unique()->word,
    ];
});
