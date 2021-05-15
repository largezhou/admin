<?php

namespace Database\Factories;

use App\Models\ConfigCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConfigCategoryFactory extends Factory
{
    protected $model = ConfigCategory::class;

    public function definition()
    {
        return [
            'name' => 'cate_'.$this->faker->unique()->word,
            'slug' => 'slug_'.$this->faker->unique()->word,
        ];
    }
}
