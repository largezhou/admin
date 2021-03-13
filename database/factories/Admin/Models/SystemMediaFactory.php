<?php

namespace Database\Factories\Admin\Models;

use App\Admin\Models\SystemMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

class SystemMediaFactory extends Factory
{
    protected $model = SystemMedia::class;

    public function definition()
    {
        $ext = mt_rand(0, 10) > 8 ? '' : $this->faker->randomElement(['jpg', 'gif', 'txt', 'php', 'png', 'js']);
        $filename = $this->faker->unique()->word.($ext ? ".{$ext}" : '');

        $dimensions = [100, 150, 200, 350, 400, 500];

        return [
            'filename' => $filename,
            'ext' => $ext,
            'category_id' => 0,
            'path' => '/'.$this->faker->randomElement($dimensions).'x'.$this->faker->randomElement($dimensions).'/'.substr($this->faker->hexColor, 1),
            'size' => $this->faker->numberBetween(500, 102400),
            'mime_type' => $ext ? $this->faker->mimeType : null,
        ];
    }
}
