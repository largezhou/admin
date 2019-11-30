<?php

use App\Admin\Models\SystemMedia;
use App\Admin\Models\SystemMediaCategory;
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
        $media = factory(SystemMedia::class, 20)->create();

        $cateIds = SystemMediaCategory::pluck('id')->toArray();
        if (!empty($cateIds)) {
            $faker = app(Faker\Generator::class);
            $media->each(function (SystemMedia $i) use ($cateIds, $faker) {
                $i->update(['category_id' => $faker->randomElement($cateIds)]);
            });
        }
    }
}
