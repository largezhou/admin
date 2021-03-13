<?php

namespace Database\Seeders;

use App\Admin\Models\SystemMedia;
use App\Admin\Models\SystemMediaCategory;
use Faker\Generator;
use Illuminate\Database\Seeder;

class SystemMediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $media = SystemMedia::factory(20)->create();

        $cateIds = SystemMediaCategory::pluck('id')->toArray();
        if (!empty($cateIds)) {
            $faker = app(Generator::class);
            $media->each(function (SystemMedia $i) use ($cateIds, $faker) {
                $i->update(['category_id' => $faker->randomElement($cateIds)]);
            });
        }
    }
}
