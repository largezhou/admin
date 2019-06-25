<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;
use App\Models\AdminUser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

$factory->define(AdminUser::class, function (Faker $faker) {
    static $pw;
    static $avatar;

    if (!$avatar) {
        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);
        $driver = Storage::disk('uploads');

        $avatar = $driver->url($driver->putFileAs(
            'admin/avatar',
            $file,
            md5_file($file).'.jpg'
        ));
    }

    return [
        'username' => $faker->userName,
        'password' => $pw ?: ($pw = bcrypt('000000')),
        'name' => $faker->name,
        'avatar' => $avatar,
    ];
});
