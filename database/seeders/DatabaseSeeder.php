<?php

namespace Database\Seeders;

namespace Database\Seeders;

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
        // \App\Models\User::factory(10)->create();

        $this->call(AdminUsersTableSeeder::class);
        $this->call(AdminPermissionsTableSeeder::class);
        $this->call(AdminRolesTableSeeder::class);
        $this->call(AdminRolePermissionTableSeeder::class);
        $this->call(AdminUserRoleAndPermissionTableSeeder::class);
        $this->call(SystemMediaCategoriesTableSeeder::class);
        $this->call(SystemMediaTableSeeder::class);
        $this->call(ConfigCategoriesTableSeeder::class);
        $this->call(ConfigsTableSeeder::class);
    }
}
