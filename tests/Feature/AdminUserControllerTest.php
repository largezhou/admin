<?php

namespace Tests\Feature;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;
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
        $res = $this->get(route('admin.user'));
        $res->assertStatus(200)
            ->assertJsonFragment(['id' => $this->user->id]);
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

    public function testStoreValidation()
    {
        // username, name, password required
        // roles.*, permissions.*, exists
        $res = $this->storeResource([
            'roles' => [9999],
            'permissions' => [9999],
        ]);
        $res->assertJsonValidationErrors([
            'username',
            'name',
            'password',
            'roles.0',
            'permissions.0',
        ]);

        // username, name max:100
        // password max:20
        $res = $this->storeResource([
            'username' => str_repeat('e', 101),
            'name' => str_repeat('e', 101),
            'password' => str_repeat('e', 21),
        ]);
        $res->assertJsonValidationErrors(['username', 'name', 'password']);

        // username unique
        // password min:6
        $res = $this->storeResource([
            'username' => 'admin',
            'password' => str_repeat('e', 5),
        ]);
        $res->assertJsonValidationErrors(['username', 'password']);

        // password confirmed
        $res = $this->storeResource([
            'password' => 'password',
            'password_confirmation' => 'not match',
        ]);
        $res->assertJsonValidationErrors(['password']);
    }

    public function testStore()
    {
        factory(AdminRole::class, 5)->create();
        factory(AdminPermission::class, 5)->create();
        $pw = '000000';
        $userInputs = factory(AdminUser::class)->make(['password' => $pw])->toArray();

        $res = $this->storeResource($userInputs + [
                'password_confirmation' => $pw,
                'roles' => [1, 2, 3],
                'permissions' => [4, 5],
            ]);
        $res->assertStatus(201);

        $this->assertDatabaseHas('admin_users', [
            'id' => 2,
            'username' => $userInputs['username'],
            'name' => $userInputs['name'],
        ]);
        $this->assertTrue(Hash::check($pw, AdminUser::find(2)->password));

        $this->assertDatabaseHas('admin_user_role', [
            'user_id' => 2,
            'role_id' => 1,
        ]);
        $this->assertDatabaseHas('admin_user_permission', [
            'user_id' => 2,
            'permission_id' => 4,
        ]);
    }

    public function testShow()
    {
        $this->user->roles()->attach(factory(AdminRole::class, 3)->create()->pluck('id'));
        $this->user->permissions()->attach(factory(AdminPermission::class, 3)->create()->pluck('id'));

        $res = $this->getResource(1);
        $res->assertStatus(200)
            ->assertJsonCount(3, 'roles')
            ->assertJsonCount(3, 'permissions');
    }
}
