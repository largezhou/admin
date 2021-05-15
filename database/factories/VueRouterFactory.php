<?php

namespace Database\Factories;

use App\Models\VueRouter;
use Illuminate\Database\Eloquent\Factories\Factory;

class VueRouterFactory extends Factory
{
    protected $model = VueRouter::class;

    public function definition()
    {
        $icons = ['md-add', 'md-alarm', 'md-albums', 'md-body', 'md-card'];
        return [
            'parent_id' => 0,
            'order' => $this->faker->numberBetween(0, 100),
            'title' => '菜单'.$this->faker->unique()->name,
            'icon' => $this->faker->randomElement($icons),
            'path' => '/'.fake_path(),
            'cache' => $this->faker->randomElement([0, 1]),
            'menu' => $this->faker->randomElement([0, 1]),
            'permission' => null,
        ];
    }
}
