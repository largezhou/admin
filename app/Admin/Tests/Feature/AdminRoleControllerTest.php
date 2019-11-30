<?php

namespace App\Admin\Tests\Feature;

use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use Illuminate\Support\Arr;
use App\Admin\Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Admin\Tests\Traits\RequestActions;

class AdminRoleControllerTest extends AdminTestCase
{
    use RefreshDatabase;
    use RequestActions;
    use WithFaker;
    protected $resourceName = 'admin-roles';

    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    public function testStoreValidation()
    {
        // name, slug required
        // permissions array
        $res = $this->storeResource([
            'name' => '',
            'slug' => '',
            'permissions' => 'not array',
        ]);
        $res->assertJsonValidationErrors(['name', 'slug', 'permissions']);

        factory(AdminRole::class)->create([
            'name' => 'name',
            'slug' => 'slug',
        ]);
        factory(AdminPermission::class)->create();
        // name, slug unique
        // permissions.* exists
        $res = $this->storeResource([
            'name' => 'name',
            'slug' => 'slug',
            'permissions' => [99],
        ]);
        $res->assertJsonValidationErrors(['name', 'slug', 'permissions']);
    }

    public function testStore()
    {
        $inputs = [
            'name' => 'name',
            'slug' => 'slug',
        ];
        $res = $this->storeResource($inputs);
        $res->assertStatus(201);
        $this->assertDatabaseHas('admin_roles', $inputs);

        $inputs = [
            'name' => 'name1',
            'slug' => 'slug2',
        ];
        $permissionId = factory(AdminPermission::class)->create()->id;
        $res = $this->storeResource($inputs + [
                'permissions' => [$permissionId],
            ]);
        $res->assertStatus(201);

        $this->assertDatabaseHas('admin_role_permission', [
            'role_id' => $this->getLastInsertId('admin_roles'),
            'permission_id' => $permissionId,
        ]);
    }

    /**
     * 生成一个角色，并关联一个权限，然后返回角色和权限的 id
     *
     * @return array [$roleId, $permissionId]
     */
    protected function createRole()
    {
        $role = factory(AdminRole::class)->create();
        $permissionId = factory(AdminPermission::class)->create()->id;

        $role->permissions()->attach($permissionId);

        return [$role->id, $permissionId];
    }

    public function testEdit()
    {
        [$roleId, $_] = $this->createRole();

        $res = $this->editResource($roleId);
        $res->assertStatus(200)
            ->assertJsonFragment(AdminRole::first()->toArray())
            ->assertJsonCount(1, 'permissions');
    }

    public function testUpdate()
    {
        [$roleId1, $permissionId1] = $this->createRole();
        // 清空关联权限
        // 不更新任何字段
        $inputs = AdminRole::first()->toArray();
        $res = $this->updateResource($roleId1, $inputs + ['permissions' => []]);
        $res->assertStatus(201);
        $this->assertDatabaseHas('admin_roles', $inputs);
        $this->assertDatabaseMissing('admin_role_permission', [
            'role_id' => $roleId1,
            'permission_id' => $permissionId1,
        ]);

        [$roleId2, $permissionId2] = $this->createRole();
        // 更新字段和权限
        $inputs = [
            'name' => 'new name',
            'slug' => 'new slug',
            'permissions' => [$permissionId1], // 取消原来的权限 2，更换为权限 1
        ];
        $res = $this->updateResource($roleId2, $inputs);
        $res->assertStatus(201);
        $this->assertDatabaseHas('admin_roles', Arr::except($inputs, 'permissions'));
        $this->assertDatabaseHas('admin_role_permission', [
            'role_id' => $roleId2,
            'permission_id' => $permissionId1,
        ]);
        $this->assertDatabaseMissing('admin_role_permission', [
            'role_id' => $roleId2,
            'permission_id' => $permissionId2,
        ]);

        // 移除全部权限
        $res = $this->updateResource($roleId2, ['permissions' => []]);
        $res->assertStatus(201);
        $this->assertDatabaseMissing('admin_role_permission', [
            'role_id' => $roleId2,
        ]);
    }

    public function testDestroy()
    {
        [$roleId, $permissionId] = $this->createRole();
        $res = $this->destroyResource($roleId);
        $res->assertStatus(204);
        $this->assertDatabaseMissing('admin_roles', ['id' => $roleId]);
        $this->assertDatabaseMissing('admin_role_permission', [
            'role_id' => $roleId,
            'permission_id' => $permissionId,
        ]);
    }

    public function testIndex()
    {
        factory(AdminRole::class, 20);
        factory(AdminPermission::class, 5);
        app(\AdminRolePermissionTableSeeder::class)->run();
        $res = $this->getResources();
        $res->assertStatus(200)
            ->assertJsonCount(15, 'data')
            // 角色对应的权限数
            ->assertJsonCount(AdminRole::orderByDesc('id')->first()->permissions->count(), 'data.0.permissions');

        // 测试筛选
        factory(AdminRole::class)
            ->create([
                'name' => 'role name query',
                'slug' => 'role slug query',
            ])
            ->permissions()
            ->create(factory(AdminPermission::class)->create(['name' => 'perm name query'])->toArray());
        $res = $this->getResources([
            'id' => $this->getLastInsertId('admin_roles'),
            'name' => 'role name',
            'slug' => 'role slug',
        ]);
        $res->assertJsonCount(1, 'data');

        // 权限名称筛选
        $res = $this->getResources(['permission_name' => 'perm name']);
        $res->assertJsonCount(1, 'data');
        $res = $this->getResources(['permission_name' => 'nothing']);
        $res->assertJsonCount(0, 'data');
    }

    public function testCreate()
    {
        factory(AdminPermission::class)->create(['slug' => 'slug']);

        $res = $this->createResource();
        $res->assertStatus(200)
            ->assertJsonFragment([
                'slug' => 'slug',
            ]);
    }
}
