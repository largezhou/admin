<?php

namespace Database\Factories;

use App\Models\Config;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConfigFactory extends Factory
{
    protected $model = Config::class;

    public function definition()
    {
        [$type, $options] = $this->makeTypeAndOptions();

        return [
            'category_id' => 0,
            'type' => $type,
            'name' => 'name_'.$this->faker->unique()->word,
            'slug' => 'slug_'.$this->faker->unique()->word,
            'desc' => (mt_rand(0, 9) > 8) ? ('desc '.$this->faker->paragraph) : null,
            'options' => $options,
            'value' => null,
            'validation_rules' => $this->faker->randomElement(['required', 'max:5', 'min:5']),
        ];
    }

    protected function makeTypeAndOptions()
    {
        $type = $this->faker->randomElement(array_keys(Config::$typeMap));
        $options = null;

        switch ($type) {
            case Config::TYPE_INPUT:
            case Config::TYPE_TEXTAREA:
            case Config::TYPE_OTHER:
                $options = null;
                break;
            case Config::TYPE_FILE:
                $options = [
                    'max' => mt_rand(1, 99),
                    'ext' => null,
                ];
                break;
            case Config::TYPE_SINGLE_SELECT:
            case Config::TYPE_MULTIPLE_SELECT:
                $values = $this->faker->randomElements(['', 1, 2, 3, 4, 5], mt_rand(1, 6));
                $pairs = array_map(function ($i) {
                    $label = $i ? ('值'.$i) : '无';
                    return $i.'=>'.$label;
                }, $values);

                $options = [
                    'options' => implode("\n", $pairs),
                    'type' => $this->faker->randomElement(['input', 'select']),
                ];
                break;
            default:
                // do nothing
        }

        return [$type, $options];
    }
}
