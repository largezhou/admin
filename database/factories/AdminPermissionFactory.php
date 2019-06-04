<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Admin\AdminPermission;
use Faker\Generator as Faker;

$factory->define(AdminPermission::class, function (Faker $faker) {
    $httpPaths = [];
    for ($i = 0; $i <= $faker->numberBetween(1, 3); $i++) {
        $httpPaths[] = '/'.fake_path();
    }
    $httpPaths = implode("\r\n", $httpPaths);

    return [
        'name' => 'perm_'.$faker->unique()->word,
        'slug' => $faker->unique()->word,
        'http_method' => $faker->randomElements(AdminPermission::$httpMethods, $faker->numberBetween(0, 3)),
        'http_path' => $httpPaths,
    ];
});
