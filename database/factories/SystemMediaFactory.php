<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Admin\Models\SystemMedia;
use Faker\Generator as Faker;

$factory->define(SystemMedia::class, function (Faker $faker) {
    $ext = mt_rand(0, 10) > 8 ? '' : $faker->randomElement(['jpg', 'gif', 'txt', 'php', 'png', 'js']);
    $filename = $faker->unique()->word.($ext ? ".{$ext}" : '');

    $dimensions = [100, 150, 200, 350, 400, 500];

    return [
        'filename' => $filename,
        'ext' => $ext,
        'category_id' => 0,
        'path' => '/'.$faker->randomElement($dimensions).'x'.$faker->randomElement($dimensions).'/'.substr($faker->hexColor, 1),
        'size' => $faker->numberBetween(500, 102400),
        'mime_type' => $ext ? $faker->mimeType : null,
    ];
});
