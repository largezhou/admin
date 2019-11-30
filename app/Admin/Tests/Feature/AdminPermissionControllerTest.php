<?php

namespace App\Admin\Tests\Feature;

use App\Admin\Models\AdminPermission;
use App\Admin\Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Admin\Tests\Traits\RequestActions;

class AdminPermissionControllerTest extends AdminTestCase
{
    use RefreshDatabase;
    use WithFaker;
    use RequestActions;
    protected $resourceName = 'admin-permissions';

    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    public function testStoreValidation()
    {
        // name slug required
        // http_method array
        // http_path valid
        $res = $this->storeResource([
            'name' => '',
            'slug' => '',
            'http_method' => 'not array',
            'http_path' => "ERR:err/err",
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug', 'http_method', 'http_path']);

        factory(AdminPermission::class)->create(['slug' => 'slug']);
        factory(AdminPermission::class)->create(['name' => 'name']);
        // name slug unique
        // http_method.* in
        $res = $this->storeResource([
            'name' => 'name',
            'slug' => 'slug',
            'http_method' => ['GET', 'TEST'],
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug', 'http_method.1']);
    }

    public function testStore()
    {
        /** @var AdminPermission $model */
        $model = factory(AdminPermission::class)->make();
        $this->assertStore($model);

        // http_method 和 http_path 为空
        $model = factory(AdminPermission::class)->make([
            'http_method' => null,
            'http_path' => null,
        ]);
        $this->assertStore($model);
    }

    protected function assertStore(AdminPermission $model)
    {
        $inputs = $model->toArray();
        $inputs['http_path'] = implode("\n", $inputs['http_path']);
        $res = $this->storeResource($inputs);
        $res->assertStatus(201);

        $this->assertDatabaseHas('admin_permissions', array_merge($model->getAttributes()));
    }

    public function testIndex()
    {
        factory(AdminPermission::class, 20)->create();

        $res = $this->getResources();
        $res->assertStatus(200)
            ->assertJsonFragment(['total' => 20])
            ->assertJsonFragment(['last_page' => 2]);

        // 筛选
        $id = factory(AdminPermission::class)->create([
            'http_path' => 'path/to/query',
            'slug' => 'slug query',
            'name' => 'name query',
        ])->id;

        $res = $this->getResources([
            'id' => $id,
        ]);
        $res->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['id' => $id]);

        $res = $this->getResources([
            'id' => $id,
            'http_path' => 'to',
            'slug' => 'slug',
            'name' => 'name',
        ]);
        $res->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['id' => $id]);

        // 测试不分页 和 只包含特定字段
        $res = $this->getResources([
            'all' => 1,
            'only' => ['id', 'name'],
        ]);
        $res->assertJsonCount(21);
    }

    public function testEdit()
    {
        $res = $this->editResource(99999);
        $res->assertStatus(404);

        $id = factory(AdminPermission::class)->create()->id;
        $res = $this->editResource($id);
        $res->assertStatus(200)
            ->assertJsonFragment(['id' => $id]);
    }

    public function testUpdate()
    {
        // id = 1
        $id1 = factory(AdminPermission::class)->create()->id;
        // id = 2
        $id2 = factory(AdminPermission::class)->create([
            'name' => 'name',
            'slug' => 'slug',
        ])->id;

        $res = $this->updateResource($id1, [
            'name' => 'name',
            'slug' => 'slug',
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug']);

        $inputs = [
            'slug' => 'new slug',
            'http_path' => null,
            'http_method' => null,
        ];
        $res = $this->updateResource($id2, $inputs);
        $res->assertStatus(201);

        $this->assertDatabaseHas('admin_permissions', $inputs + ['id' => $id2, 'name' => 'name']);
    }

    public function testDestroy()
    {
        $id = factory(AdminPermission::class)->create()->id;

        $res = $this->destroyResource($id);
        $res->assertStatus(204);

        $this->assertDatabaseMissing('admin_permissions', ['id' => $id]);
    }
}
