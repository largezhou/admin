<?php

use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use App\Admin\Models\AdminUser;
use Illuminate\Database\Seeder;

class AdminUserRoleAndPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = AdminUser::all();
        if ($users->isEmpty()) {
            $this->call(AdminUsersTableSeeder::class);
            $users = AdminUser::all();
        }

        $roles = AdminRole::pluck('id');
        if ($roles->isEmpty()) {
            $this->call(AdminRolesTableSeeder::class);
            $roles = AdminRole::pluck('id');
        }

        $perms = AdminPermission::pluck('id');
        if ($perms->isEmpty()) {
            $this->call(AdminPermissionsTableSeeder::class);
            $perms = AdminPermission::pluck('id');
        }

        $faker = app(Faker\Generator::class);
        $users->each(function (AdminUser $user) use ($faker, $roles) {
            $user->roles()
                ->attach($faker->randomElements($roles, $faker->numberBetween(0, 5)));
        });
        $users->each(function (AdminUser $user) use ($faker, $perms) {
            $user->permissions()
                ->attach($faker->randomElements($perms, $faker->numberBetween(0, 5)));
        });
    }
}
