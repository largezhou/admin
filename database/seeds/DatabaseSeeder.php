<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminUsersTableSeeder::class);
        $this->call(AdminPermissionsTableSeeder::class);
        $this->call(AdminRolesTableSeeder::class);
        $this->call(AdminRolePermissionTableSeeder::class);
        $this->call(AdminUserRoleAndPermissionTableSeeder::class);
        $this->call(SystemMediaCategoriesTableSeeder::class);
        $this->call(SystemMediaTableSeeder::class);
    }
}
