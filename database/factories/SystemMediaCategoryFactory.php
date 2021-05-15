<?php

namespace Database\Factories;

use App\Models\SystemMediaCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class SystemMediaCategoryFactory extends Factory
{
    protected $model = SystemMediaCategory::class;

    public function definition()
    {
        return [
            'parent_id' => 0,
            'name' => 'cate_'.$this->faker->unique()->word,
            'folder' => implode('/', $this->faker->words(2)),
        ];
    }
}
