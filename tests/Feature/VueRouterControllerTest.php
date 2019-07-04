<?php

namespace Tests\Feature;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\VueRouter;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AdminTestCase;
use Tests\Traits\RequestActions;

class VueRouterControllerTest extends AdminTestCase
{
    use RefreshDatabase;
    use WithFaker;
    use RequestActions;
    protected $resourceName = 'vue-routers';

    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    public function testStoreValidation()
    {
        // title required
        // order integer
        // cache menu boolean
        // permission exists
        // roles.* exists
        $res = $this->storeResource([
            'title' => '',
            'order' => 15.1,
            'cache' => 'not bool',
            'menu' => 'not bool',
            'permission' => 'not exists',
            'roles' => [999],
        ]);
        $res->assertJsonValidationErrors(['title', 'order', 'cache', 'menu', 'permission', 'roles.0']);

        // permission nullable
        $res = $this->storeResource([
            'permission' => '',
        ]);
        $res->assertJsonMissingValidationErrors(['permission']);

        // max
        $res = $this->storeResource([
            'title' => str_repeat('a', 51),
            'icon' => str_repeat('a', 51),
            'path' => str_repeat('a', 51),
            'order' => 10000,
        ]);
        $res->assertJsonValidationErrors(['icon', 'path', 'title', 'order']);

        // order min
        $res = $this->storeResource([
            'order' => -10000,
        ]);
        $res->assertJsonValidationErrors(['order']);

        factory(VueRouter::class)->create();
        // parent_id exists
        $res = $this->storeResource([
            'parent_id' => 999,
        ]);
        $res->assertJsonValidationErrors('parent_id');
    }

    public function testStore()
    {
        factory(VueRouter::class)->create();
        factory(AdminPermission::class)->create(['slug' => 'slug']);
        factory(AdminRole::class)->create();
        $inputs = factory(VueRouter::class)->make([
            'parent_id' => 1,
            'path' => 'no/start/slash',
            'permission' => 'slug',
        ])->toArray();

        $res = $this->storeResource($inputs + ['roles' => [1]]);
        $res->assertStatus(201);

        $this->assertDatabaseHas(
            'vue_routers',
            array_merge($inputs, [
                'id' => 2,
                'parent_id' => 1,
                'permission' => 'slug',
            ])
        );
        $this->assertDatabaseHas('vue_router_role', [
            'vue_router_id' => 2,
            'role_id' => 1,
        ]);

        // 不传 parent_id 默认为 0
        $inputs['parent_id'] = null;
        $res = $this->storeResource($inputs);
        $res->assertStatus(201);
        $this->assertDatabaseHas('vue_routers', [
            'id' => 3,
            'parent_id' => 0,
        ]);
    }

    public function testUpdate()
    {
        $this->updateResource(999)->assertStatus(404);
        factory(VueRouter::class, 2)->create();
        factory(AdminRole::class, 2)->create();
        VueRouter::find(1)->roles()->attach(1);

        $inputs = [
            'parent_id' => 2,
            'title' => 'new title',
            'icon' => 'new icon',
            'path' => 'new/path',
            'order' => 99,
        ];
        $res = $this->updateResource(1, $inputs + ['roles' => [2]]);
        $res->assertStatus(201);

        $this->assertDatabaseHas('vue_routers', ['id' => 1] + $inputs);
        $this->assertDatabaseHas('vue_router_role', [
            'vue_router_id' => 1,
            'role_id' => 2,
        ]);
        $this->assertDatabaseMissing('vue_router_role', [
            'vue_router_id' => 1,
            'role_id' => 1,
        ]);

        $res = $this->updateResource(1, ['roles' => []]);
        $res->assertStatus(201);
        $this->assertDatabaseMissing('vue_router_role', [
            'vue_router_id' => 1,
            'role_id' => 2,
        ]);
    }

    public function testEdit()
    {
        $res = $this->editResource(999);
        $res->assertStatus(404);

        $router = factory(VueRouter::class)->create()->toArray();
        $res = $this->editResource(1);
        $res->assertStatus(200)
            ->assertJsonFragment($router);
    }

    public function testIndex()
    {
        app(\VueRoutersTableSeeder::class)->run();
        // 手动查出 3 级嵌套菜单
        $vueRouter = VueRouter::with(['children', 'children.children'])->find(2);
        $vueRouter = $vueRouter->toArray();

        $res = $this->getResources();
        $res->assertStatus(200)
            ->assertJsonFragment($vueRouter);

        // 第二级路由 id
        $except = $vueRouter['children'][0]['id'];
        $fragment = $vueRouter;
        // 排除了第二级的 id 后，该路由和所有子路由都不会返回了
        unset($fragment['children']);

        $res = $this->getResources(['except' => $except]);
        $res->assertStatus(200)
            ->assertJsonFragment($fragment);
    }

    public function testDestroy()
    {
        $this->destroyResource(999)->assertStatus(404);

        app(\VueRoutersTableSeeder::class)->run();
        $vueRouter = VueRouter::with(['children', 'children.children'])->find(2);

        $this->destroyResource($vueRouter->id)->assertStatus(204);

        // 子菜单全部删除
        $this->assertDatabaseMissing('vue_routers', ['id' => $vueRouter->id]);
        // hack 一下，无妨，，，
        $this->assertDatabaseMissing('vue_routers', ['id' => $vueRouter['children'][0]['id']]);
        $this->assertDatabaseMissing('vue_routers', ['id' => $vueRouter['children'][0]['children'][0]['id']]);
    }
}
