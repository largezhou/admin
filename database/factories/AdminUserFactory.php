<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;
use App\Admin\Models\AdminUser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

$factory->define(AdminUser::class, function (Faker $faker) {
    static $pw;

    return [
        'username' => $faker->userName,
        'password' => $pw ?: ($pw = bcrypt('000000')),
        'name' => $faker->name,
    ];
});
