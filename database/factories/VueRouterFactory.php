<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Admin\Models\VueRouter;
use Faker\Generator as Faker;

$factory->define(VueRouter::class, function (Faker $faker) {
    $icons = ['md-add', 'md-alarm', 'md-albums', 'md-body', 'md-card'];
    return [
        'parent_id' => 0,
        'order' => $faker->numberBetween(0, 100),
        'title' => '菜单'.$faker->unique()->name,
        'icon' => $faker->randomElement($icons),
        'path' => '/'.fake_path(),
        'cache' => $faker->randomElement([0, 1]),
        'menu' => $faker->randomElement([0, 1]),
        'permission' => null,
    ];
});
