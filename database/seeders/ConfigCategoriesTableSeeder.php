<?php

namespace Database\Seeders;

use App\Models\ConfigCategory;
use Illuminate\Database\Seeder;

class ConfigCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ConfigCategory::factory(10)->create();
    }
}
