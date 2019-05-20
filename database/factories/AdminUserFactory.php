<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;
use App\Models\AdminUser;

$factory->define(AdminUser::class, function (Faker $faker) {
    static $pw;

    return [
        'username' => $faker->userName,
        'password' => $pw ?: ($pw = bcrypt('000000')),
        'name' => $faker->name,
    ];
});
