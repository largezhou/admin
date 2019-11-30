<?php

use App\Admin\Models\AdminUser;
use Illuminate\Database\Seeder;

class AdminUsersTableSeeder extends Seeder
{
    public function run()
    {
        factory(AdminUser::class, 10)->create();

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
