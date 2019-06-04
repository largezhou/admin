<?php

use App\Models\Admin\AdminPermission;
use Illuminate\Database\Seeder;

class AdminPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(AdminPermission::class, 10)->create();
    }
}
