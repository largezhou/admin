<?php

use App\Admin\Models\SystemMediaCategory;
use Illuminate\Database\Seeder;

class SystemMediaCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 5 个一级分类
        $cates = factory(SystemMediaCategory::class, 5)->create();
        // 每个一级分类下，两个二级分类
        $cates->each(function (SystemMediaCategory $i) {
            $i->children()->createMany(factory(SystemMediaCategory::class, 2)->make()->toArray());
        });
    }
}
