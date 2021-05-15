<?php

namespace Database\Factories;

use App\Models\AdminRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminRoleFactory extends Factory
{
    protected $model = AdminRole::class;

    public function definition()
    {
        return [
            'name' => 'role_'.$this->faker->unique()->word,
            'slug' => $this->faker->unique()->word,
        ];
    }
}
