<?php

namespace Tests\Feature;

use App\Http\Resources\AdminUserResource;
use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\AdminUser;
use Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\RequestActions;

class AdminUserControllerTest extends AdminTestCase
{
    use RefreshDatabase;
    use RequestActions;
    protected $resourceName = 'admin-users';

    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    public function testUser()
    {
        $user = $this->user;
        $res = $this->get(route('admin.user'));
        $res->assertJson(AdminUserResource::make($user)->toArray(app('request')));
    }

    public function testIndex()
    {
        factory(AdminUser::class, 20)->create();
        factory(AdminPermission::class, 20)->create();
        factory(AdminRole::class, 10)->create();

        $this->user->roles()->attach([1, 2, 3]);
        $this->user->permissions()->attach([1, 2, 3]);
        $res = $this->getResources([
            'page' => 2,
        ]);
        $res->assertStatus(200)
            ->assertJsonCount(6, 'data')
            ->assertJsonCount(3, 'data.5.roles')
            ->assertJsonCount(3, 'data.5.permissions');

        // 只测试权限和角色名搜索
        $res = $this->getResources([
            'role_name' => 'nothing',
        ]);
        $res->assertStatus(200)
            ->assertJsonCount(0, 'data');
        $res = $this->getResources([
            'permission_name' => 'nothing',
        ]);
        $res->assertStatus(200)
            ->assertJsonCount(0, 'data');
        $res = $this->getResources([
            'role_name' => AdminRole::find(1)->value('name'),
            'permission_name' => AdminPermission::find(1)->value('name'),
        ]);
        $res->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
