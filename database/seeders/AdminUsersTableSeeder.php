<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;

class AdminUsersTableSeeder extends Seeder
{
    public function run()
    {
        AdminUser::factory(10)->create();

        // 整一个用户名为 admin 的用户
        if (!$admin = AdminUser::query()->where('username', 'admin')->first()) {
            $admin = AdminUser::query()->first();
        }
        $admin->update([
            'username' => 'admin',
            'name' => '超管',
        ]);
    }
}
