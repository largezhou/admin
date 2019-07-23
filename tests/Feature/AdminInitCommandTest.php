<?php

namespace Tests\Feature;

use App\Console\Commands\AdminInitCommand;
use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\AdminUser;
use App\Models\VueRouter;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminInitCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testInit()
    {
        $this->artisan('admin:init')
            ->expectsQuestion(AdminInitCommand::$initConfirmTip, false)
            ->assertExitCode(1);

        $this->artisan('admin:init')
            ->expectsQuestion(AdminInitCommand::$initConfirmTip, true)
            ->assertExitCode(0);

        $this->assertDatabaseHas('vue_routers', [
            'id' => 1,
            'path' => 'index',
            'title' => '首页',
        ]);
        $this->assertDatabaseHas('admin_users', [
            'username' => 'admin',
        ]);
        $this->assertDatabaseHas('admin_roles', [
            'slug' => 'administrator',
        ]);
        $this->assertDatabaseHas('admin_permissions', [
            'slug' => 'pass-all',
            'http_path' => '*',
        ]);

        // 命令中用了 truncate，会导致回滚失败，所以手动清理一下
        VueRouter::truncate();
        AdminUser::truncate();
        AdminRole::truncate();
        AdminPermission::truncate();
        collect(['admin_role_permission', 'admin_user_permission', 'admin_user_role', 'vue_router_role'])
            ->each(function ($table) {
                DB::table($table)->truncate();
            });
    }
}
