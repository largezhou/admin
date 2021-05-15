<?php

namespace Database\Seeders;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use Faker\Generator;
use Illuminate\Database\Seeder;

class AdminRolePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = AdminRole::all();
        if ($roles->isEmpty()) {
            $this->call(AdminRolesTableSeeder::class);
            $roles = AdminRole::all();
        }

        $perms = AdminPermission::pluck('id');
        if ($perms->isEmpty()) {
            $this->call(AdminPermissionsTableSeeder::class);
            $perms = AdminPermission::pluck('id');
        }

        $faker = app(Generator::class);
        $roles->each(function (AdminRole $role) use ($faker, $perms) {
            $role->permissions()
                ->attach($faker->randomElements($perms, $faker->numberBetween(0, 5)));
        });
    }
}
