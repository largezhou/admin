<?php

namespace Database\Factories;

use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminUserFactory extends Factory
{
    protected $model = AdminUser::class;

    protected static $pw;

    public function definition()
    {
        return [
            'username' => $this->faker->userName,
            'password' => static::$pw ?: (static::$pw = bcrypt('000000')),
            'name' => $this->faker->name,
        ];
    }
}
