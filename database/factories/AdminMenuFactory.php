<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Admin\AdminMenu;
use Faker\Generator as Faker;

$factory->define(AdminMenu::class, function (Faker $faker) {
    $icons = ['md-add', 'md-alarm', 'md-albums', 'md-body', 'md-card'];
    return [
        'order' => $faker->numberBetween(0, 100),
        'title' => '菜单'.$faker->unique()->name,
        'icon' => $faker->randomElement($icons),
        'uri' => implode('/', $faker->words($faker->numberBetween(2, 4))),
    ];
});
