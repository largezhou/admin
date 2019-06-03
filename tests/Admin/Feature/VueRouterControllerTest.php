<?php

namespace Tests\Admin\Feature;

use App\Models\Admin\VueRouter;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VueRouterControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    protected function postStore($data = [])
    {
        return $this->post(route('admin.vue-routers.store'), $data);
    }

    protected function putUpdate($id, $data = [])
    {
        return $this->put(route('admin.vue-routers.update', $id), $data);
    }

    protected function getEdit($id)
    {
        return $this->get(route('admin.vue-routers.edit', $id));
    }

    protected function getIndex()
    {
        return $this->get(route('admin.vue-routers.index'));
    }

    protected function destroy($id)
    {
        return $this->delete(route('admin.vue-routers.destroy', $id));
    }

    public function testStoreValidation()
    {
        // title required
        // order integer
        // cache is_menu boolean
        $res = $this->postStore([
            'title' => '',
            'order' => 15.1,
            'cache' => 'not bool',
            'is_menu' => 'not bool',
        ]);
        $res->assertJsonValidationErrors(['title', 'order', 'cache', 'is_menu']);

        // max
        $res = $this->postStore([
            'title' => str_repeat('a', 51),
            'icon' => str_repeat('a', 51),
            'path' => str_repeat('a', 51),
            'order' => 10000,
        ]);
        $res->assertJsonValidationErrors(['icon', 'path', 'title', 'order']);

        // order min
        $res = $this->postStore([
            'order' => -10000,
        ]);
        $res->assertJsonValidationErrors(['order']);

        factory(VueRouter::class)->create();
        // parent_id exists
        $res = $this->postStore([
            'parent_id' => 999,
        ]);
        $res->assertJsonValidationErrors('parent_id');
    }

    public function testStore()
    {
        factory(VueRouter::class)->create();
        $inputs = factory(VueRouter::class)->make([
            'parent_id' => 1,
            'path' => 'no/start/slash',
        ])->toArray();
        $inputs['created_at'] = (string) now()->addDay();

        $res = $this->postStore($inputs);
        $res->assertStatus(201);

        $this->assertDatabaseHas(
            'vue_routers',
            array_merge($inputs, [
                'created_at' => (string) now(),
                'id' => 2,
                'parent_id' => 1,
                'path' => '/'.$inputs['path'],
            ])
        );
    }

    public function testUpdateValidation()
    {
        // 与 store 唯一不同的是，parent_id 不能是自己
        factory(VueRouter::class, 2)->create();
        $res = $this->putUpdate(1, [
            'parent_id' => 1,
        ]);
        $res->assertJsonValidationErrors(['parent_id'])
            // 更新时，只会验证有的字段
            ->assertJsonMissingValidationErrors(['title']);
        $res = $this->putUpdate(1, [
            'parent_id' => 2,
        ]);
        $res->assertJsonMissingValidationErrors(['parent_id']);
    }

    public function testUpdate()
    {
        $this->putUpdate(999)->assertStatus(404);
        factory(VueRouter::class, 2)->create();

        $inputs = [
            'parent_id' => 2,
            'title' => 'new title',
            'icon' => 'new icon',
            'path' => 'new/path',
            'order' => 99,
        ];
        $res = $this->putUpdate(1, $inputs);
        $res->assertStatus(200);

        $inputs['path'] = '/'.$inputs['path'];
        $this->assertDatabaseHas('vue_routers', ['id' => 1] + $inputs);
    }

    public function testEdit()
    {
        $res = $this->getEdit(999);
        $res->assertStatus(404);

        $router = factory(VueRouter::class)->create()->toArray();
        $res = $this->getEdit(1);
        $res->assertStatus(200)
            ->assertJsonFragment($router);
    }

    public function testIndex()
    {
        app(\VueRoutersTableSeeder::class)->run();
        // 手动查出 3 级嵌套菜单
        $vueRouter = VueRouter::with(['children', 'children.children'])->find(2);
        $vueRouter = $vueRouter->toArray();

        $res = $this->getIndex();
        $res->assertStatus(200)
            ->assertJsonFragment($vueRouter);
    }

    public function testDestroy()
    {
        $this->delete(999)->assertStatus(404);

        app(\VueRoutersTableSeeder::class)->run();
        $vueRouter = VueRouter::with(['children', 'children.children'])->find(2);

        $this->destroy($vueRouter->id)->assertStatus(204);

        // 子菜单全部删除
        $this->assertDatabaseMissing('vue_routers', ['id' => $vueRouter->id]);
        // hack 一下，无妨，，，
        $this->assertDatabaseMissing('vue_routers', ['id' => $vueRouter['children'][0]['id']]);
        $this->assertDatabaseMissing('vue_routers', ['id' => $vueRouter['children'][0]['children'][0]['id']]);
    }
}
