<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\SystemMedia;
use Faker\Generator as Faker;

$factory->define(SystemMedia::class, function (Faker $faker) {
    $ext = mt_rand(0, 10) > 8 ? '' : $faker->fileExtension;
    $filename = $faker->unique()->word.($ext ? ".{$ext}" : '');

    return [
        'filename' => $filename,
        'ext' => $ext,
        'category_id' => 0,
        'path' => '/'.implode('/', $faker->words()).'/'.$filename,
        'size' => $faker->numberBetween(),
        'mime_type' => $ext ? $faker->mimeType : null,
    ];
});
