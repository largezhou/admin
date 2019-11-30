<?php

use App\Admin\Models\AdminRole;
use Illuminate\Database\Seeder;

class AdminRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(AdminRole::class, 20)->create();
    }
}
