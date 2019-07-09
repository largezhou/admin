<?php

namespace Tests\Feature;

use App\Console\Commands\AdminInitCommand;
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
    }
}
