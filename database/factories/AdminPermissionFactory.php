<?php

namespace Database\Factories;

use App\Models\AdminPermission;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminPermissionFactory extends Factory
{
    protected $model = AdminPermission::class;

    public function definition()
    {
        $faker = $this->faker;

        $getMethods = function () use ($faker) {
            return $faker->randomElements(AdminPermission::$httpMethods, $faker->numberBetween(0, 3));
        };

        $httpPaths = [];
        for ($i = 0; $i <= $faker->numberBetween(1, 3); $i++) {
            $specifyMethod = (mt_rand(0, 10) > 8) ? implode(',', $getMethods()).':' : '';
            $httpPaths[] = $specifyMethod.'/'.fake_path();
        }
        $httpPaths = implode("\n", $httpPaths);

        return [
            'name' => 'perm_'.$faker->unique()->word,
            'slug' => $faker->unique()->word,
            'http_method' => $getMethods(),
            'http_path' => $httpPaths,
        ];
    }
}
