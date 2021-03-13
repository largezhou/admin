<?php

namespace Database\Factories\Admin\Models;

use App\Admin\Models\SystemMediaCategory;
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
