<?php

namespace Database\Seeders;

use App\Models\ConfigCategory;
use App\Models\Config;
use Illuminate\Database\Seeder;

class ConfigsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cates = ConfigCategory::all();
        if (empty($cates)) {
            $this->call(ConfigCategoriesTableSeeder::class);
            $cates = ConfigCategory::all();
        }

        $cates->each(function (ConfigCategory $cate) {
            $cate->configs()->createMany(Config::factory(2)->make()->toArray());
        });
    }
}
