<?php

namespace Tests\Admin\Feature;

use App\Models\Admin\AdminPermission;
use Tests\Admin\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\RequestActions;

class AdminPermissionControllerTest extends TestCase
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
        $model['http_method'] = null;
        $model['http_path'] = null;
        $this->assertStore($model);
    }

    protected function assertStore(AdminPermission $model)
    {
        AdminPermission::truncate();
        $inputs = $model->toArray();
        $inputs['http_path'] = implode("\n", $inputs['http_path']);
        $res = $this->storeResource($inputs);
        $res->assertStatus(201);

        $this->assertDatabaseHas('admin_permissions', array_merge($model->getAttributes(), ['id' => 1]));
    }

    public function testIndex()
    {
        factory(AdminPermission::class, 20)->create();

        $res = $this->getResources();
        $res->assertStatus(200)
            ->assertJsonFragment(['id' => 6])
            ->assertJsonFragment(['total' => 20])
            ->assertJsonFragment(['last_page' => 2]);

        // 筛选
        factory(AdminPermission::class)->create([
            'http_path' => 'path/to/query',
            'slug' => 'slug query',
            'name' => 'name query',
        ]);
        $res = $this->getResources([
            'id' => 21,
            'http_path' => 'to',
            'slug' => 'slug',
            'name' => 'name',
        ]);
        $res->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['id' => 21]);
    }

    public function testEdit()
    {
        $res = $this->editResource(1);
        $res->assertStatus(404);

        factory(AdminPermission::class)->create();
        $res = $this->editResource(1);
        $res->assertStatus(200)
            ->assertJsonFragment(['id' => 1]);
    }

    public function testUpdate()
    {
        // id = 1
        factory(AdminPermission::class)->create();
        // id = 2
        factory(AdminPermission::class)->create([
            'name' => 'name',
            'slug' => 'slug',
        ]);

        $res = $this->updateResource(1, [
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
        $res = $this->updateResource(2, $inputs);
        $res->assertStatus(200);

        $this->assertDatabaseHas('admin_permissions', $inputs + ['id' => 2, 'name' => 'name']);
    }

    public function testDestroy()
    {
        factory(AdminPermission::class)->create();

        $res = $this->destroyResource(1);
        $res->assertStatus(204);

        $this->assertDatabaseMissing('admin_permissions', ['id' => 1]);
    }
}
