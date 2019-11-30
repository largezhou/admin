<?php

use App\Admin\Models\ConfigCategory;
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
        factory(ConfigCategory::class, 10)->create();
    }
}
