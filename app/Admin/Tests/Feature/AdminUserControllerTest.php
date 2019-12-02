<?php

namespace App\Admin\Tests\Feature;

use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use App\Admin\Models\AdminUser;
use Illuminate\Support\Facades\Hash;
use App\Admin\Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Admin\Tests\Traits\RequestActions;

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

    /**
     * 给用户绑定一个角色，该角色有一个权限。并为用户绑定另一个单独的权限
     *
     * @param AdminUser|null $user
     *
     * @return array [roleId, permissionId1, permissionId2]
     */
    protected function attachAuthToUser(AdminUser $user = null)
    {
        $user = $user ?? $this->user;

        $permission1 = factory(AdminPermission::class)->create(['slug' => 'perm1'])->id;
        $permission2 = factory(AdminPermission::class)->create(['slug' => 'perm2'])->id;
        $role = factory(AdminRole::class)->create(['slug' => 'role']);
        $role->permissions()->attach($permission1);
        $user->roles()->attach($role->id);
        $user->permissions()->attach($permission2);

        return [$role->id, $permission1, $permission2];
    }

    public function testUser()
    {
        $this->attachAuthToUser();

        $res = $this->get(route('admin.user'));
        $res->assertStatus(200)
            ->assertJsonFragment(['id' => $this->user->id])
            ->assertJsonFragment(['roles' => ['role']])
            ->assertJsonFragment(['permissions' => ['perm1', 'perm2']]);
    }

    public function testEditUser()
    {
        $this->attachAuthToUser();

        $res = $this->get(route('admin.user.edit'));
        $res->assertStatus(200)
            ->assertJsonCount(1, 'roles')
            ->assertJsonCount(1, 'permissions');
    }

    public function testUpdateUser()
    {
        $this->attachAuthToUser();

        $res = $this->put(route('admin.user.update'), [
            'name' => 'new name',
            'password' => '123456',
            'password_confirmation' => '123456',
            'username' => 'can not update',
            'roles' => [],
            'permissions' => [],
        ]);

        $res->assertStatus(201)
            ->assertSeeText('new name')
            // 账号没变
            ->assertDontSee('can not update')
            // 权限没变
            ->assertJsonFragment(['permissions' => ['perm1', 'perm2']]);

        // 密码变了
        $this->assertTrue(Hash::check('123456', $this->user->password));
    }

    public function testIndex()
    {
        factory(AdminUser::class, 20)->create();
        $permissions = factory(AdminPermission::class, 20)->create();
        $roles = factory(AdminRole::class, 10)->create();

        $this->user->roles()->attach($roles->take(3)->pluck('id'));
        $this->user->permissions()->attach($permissions->take(3)->pluck('id'));
        $res = $this->getResources([
            'page' => 2,
        ]);
        $res->assertStatus(200)
            ->assertJsonCount(6, 'data') // 第二页有 6 个数据
            ->assertJsonCount(3, 'data.5.roles') // 第二页第 5 个数据，即 $this->user
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
            'role_name' => AdminRole::first()->value('name'),
            'permission_name' => AdminPermission::first()->value('name'),
        ]);
        $res->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function testStoreValidation()
    {
        // username, name, password required
        // roles.*, permissions.*, exists
        // avatar max:255
        $res = $this->storeResource([
            'roles' => [9999],
            'permissions' => [9999],
            'avatar' => str_repeat('a', 256),
        ]);
        $res->assertJsonValidationErrors([
            'username',
            'name',
            'password',
            'roles.0',
            'permissions.0',
            'avatar',
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
        $roles = factory(AdminRole::class, 5)->create();
        $permissions = factory(AdminPermission::class, 5)->create();
        $pw = '000000';

        $userInputs = factory(AdminUser::class)->make([
            'password' => $pw,
        ])->toArray();

        $res = $this->storeResource($userInputs + [
                'password_confirmation' => $pw,
                'roles' => $roles->take(3)->pluck('id')->toArray(),
                'permissions' => $permissions->take(-2)->pluck('id')->toArray(),
            ]);
        $res->assertStatus(201);

        $userId = $this->getLastInsertId('admin_users');
        $this->assertDatabaseHas('admin_users', [
            'id' => $userId,
            'username' => $userInputs['username'],
            'name' => $userInputs['name'],
        ]);
        $this->assertTrue(Hash::check($pw, AdminUser::find($userId)->password));

        $this->assertDatabaseHas('admin_user_role', [
            'user_id' => $userId,
            'role_id' => $roles->take(3)->pluck('id')->first(),
        ]);
        $this->assertDatabaseHas('admin_user_permission', [
            'user_id' => $userId,
            'permission_id' => $permissions->take(-2)->pluck('id')->first(),
        ]);
    }

    public function testShow()
    {
        $this->user->roles()->attach(factory(AdminRole::class, 3)->create()->pluck('id'));
        $this->user->permissions()->attach(factory(AdminPermission::class, 3)->create()->pluck('id'));

        $res = $this->getResource($this->user->id);
        $res->assertStatus(200)
            ->assertJsonCount(3, 'roles')
            ->assertJsonCount(3, 'permissions');
    }

    public function testUpdate()
    {
        // 测试更新时，判断 传入的 全路径 头像，是否会替换掉数据库的相对路径
        $this->storage
            ->getDriver()
            ->getConfig()
            ->set('url', 'http://domain.com');

        $this->user->avatar = '/path/to/avatar/jpg';
        $this->user->save();

        $this->user->roles()
            ->createMany(factory(AdminRole::class, 3)->make()->toArray());
        $oldRoleId = $this->getLastInsertId('admin_roles');
        $this->user->permissions()
            ->createMany(factory(AdminPermission::class, 3)->make()->toArray());
        $oldPermissionId = $this->getLastInsertId('admin_permissions');

        $newRoles = factory(AdminRole::class, 3)->create()->pluck('id')->toArray();
        $newPerms = factory(AdminPermission::class, 3)->create()->pluck('id')->toArray();

        $userId = $this->user->id;
        $pw = 'new password';
        $res = $this->updateResource($userId, [
            'username' => 'admin',
            'name' => 'new name',
            'roles' => $newRoles,
            'permissions' => $newPerms,
            'password' => $pw,
            'password_confirmation' => $pw,
            'avatar' => $this->storage->url($this->user->avatar),
        ]);
        $res->assertStatus(201);
        $this->assertTrue(Hash::check($pw, AdminUser::find($userId)->password));
        $this->assertDatabaseHas('admin_users', [
            'id' => $userId,
            'username' => 'admin',
            'name' => 'new name',
            'avatar' => $this->user->avatar,
        ]);
        // 新角色
        $this->assertDatabaseHas('admin_user_role', [
            'user_id' => $userId,
            'role_id' => $newRoles[0],
        ]);
        // 新权限
        $this->assertDatabaseMissing('admin_user_role', [
            'user_id' => $userId,
            'role_id' => $oldRoleId,
        ]);
        // 旧角色移除
        $this->assertDatabaseHas('admin_user_permission', [
            'user_id' => $userId,
            'permission_id' => $newPerms[0],
        ]);
        // 旧权限移除
        $this->assertDatabaseMissing('admin_user_permission', [
            'user_id' => $userId,
            'permission_id' => $oldPermissionId,
        ]);

        // 移除全部角色权限
        $res = $this->updateResource($userId, [
            'roles' => [],
            'permissions' => [],
        ]);
        $res->assertStatus(201);
        $this->assertDatabaseMissing('admin_user_role', [
            'user_id' => $userId,
        ]);

        // 不填密码, 或者为空
        $pw = AdminUser::find($userId)->password;
        $res = $this->updateResource($userId, [
            'password' => '',
        ]);
        $res->assertStatus(201);
        $this->assertTrue($pw == AdminUser::find($userId)->password);
    }

    public function testDestroy()
    {
        $this->user->roles()->createMany(factory(AdminRole::class, 1)->make()->toArray());
        $this->user->permissions()->createMany(factory(AdminPermission::class, 1)->make()->toArray());

        $userId = $this->user->id;
        $res = $this->destroyResource($userId);
        $res->assertStatus(204);

        $this->assertDatabaseMissing('admin_users', ['id' => $userId]);
        $this->assertDatabaseMissing('admin_user_role', ['user_id' => $userId]);
        $this->assertDatabaseMissing('admin_user_permission', ['user_id' => $userId]);
    }

    public function testEdit()
    {
        $this->user->roles()->attach($roleIds = factory(AdminRole::class, 3)->create()->pluck('id'));
        $this->user->permissions()->attach(factory(AdminPermission::class, 3)->create()->pluck('id'));

        $res = $this->editResource($this->user->id);
        $res->assertStatus(200)
            ->assertJsonFragment(['roles' => $roleIds]);
    }

    public function testCreate()
    {
        factory(AdminRole::class)->create(['name' => 'role']);
        factory(AdminPermission::class)->create(['slug' => 'permission']);

        $res = $this->createResource();
        $res->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'role',
                'slug' => 'permission',
            ]);
    }
}
