<?php

/**
 * 生成假的路径, 文件路径, 地址路径
 *
 * @param int $min
 * @param int $max
 *
 * @return string
 */
function fake_path($min = 2, $max = 4)
{
    $faker = app(\Faker\Generator::class);
    return implode('/', $faker->words($faker->numberBetween($min, $max)));
}
