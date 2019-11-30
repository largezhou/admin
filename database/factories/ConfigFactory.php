<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Admin\Models\Config;
use Faker\Generator as Faker;

$factory->define(Config::class, function (Faker $faker) {
    list($type, $options) = _make_type_and_options($faker);

    return [
        'category_id' => 0,
        'type' => $type,
        'name' => 'name_'.$faker->unique()->word,
        'slug' => 'slug_'.$faker->unique()->word,
        'desc' => (mt_rand(0, 9) > 8) ? ('desc '.$faker->paragraph) : null,
        'options' => $options,
        'value' => null,
        'validation_rules' => $faker->randomElement(['required', 'max:5', 'min:5']),
    ];
});

if (!function_exists('_make_type_and_options')) {
    function _make_type_and_options(Faker $faker)
    {
        $type = $faker->randomElement(array_keys(Config::$typeMap));
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
                $values = $faker->randomElements(['', 1, 2, 3, 4, 5], mt_rand(1, 6));
                $pairs = array_map(function ($i) {
                    $label = $i ? ('值'.$i) : '无';
                    return $i.'=>'.$label;
                }, $values);

                $options = [
                    'options' => implode("\n", $pairs),
                    'type' => $faker->randomElement(['input', 'select']),
                ];
                break;
            default:
                // do nothing
        }

        return [$type, $options];
    }
}
