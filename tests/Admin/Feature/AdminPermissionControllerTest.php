<?php

namespace Tests\Admin\Feature;

use App\Models\Admin\AdminPermission;
use Illuminate\Foundation\Testing\TestResponse;
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
        $res = $this->storeResource([
            'name' => '',
            'slug' => '',
            'http_method' => 'not array',
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug', 'http_method']);

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

        $res = $this->storeResource($model->toArray());
        $res->assertStatus(201);

        $this->assertDatabaseHas('admin_permissions', $model->getAttributes() + ['id' => 1]);
    }

    public function testIndex()
    {
        factory(AdminPermission::class, 20)->create();

        $res = $this->getResources();
        $res->assertStatus(200)
            ->assertJsonFragment(['id' => '6'])
            ->assertJsonFragment(['total' => 20])
            ->assertJsonFragment(['last_page' => 2]);

        // 筛选
        factory(AdminPermission::class)->create([
            'http_path' => 'path/to/query',
            'http_method' => ['GET'],
            'slug' => 'slug query',
            'name' => 'name query',
        ]);
        $res = $this->getResources([
            'id' => 21,
            'http_path' => 'to',
            'http_method' => 'GET',
            'slug' => 'slug',
            'name' => 'name',
        ]);
        $res->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['id' => '21']);
    }
}
