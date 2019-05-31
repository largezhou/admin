<?php

namespace Tests\Admin\Feature;

use App\Models\Admin\AdminMenu;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminMenuControllerTest extends TestCase
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
        return $this->post(route('admin.menus.store'), $data);
    }

    protected function putUpdate($id, $data = [])
    {
        return $this->put(route('admin.menus.update', $id), $data);
    }

    protected function getEdit($id)
    {
        return $this->get(route('admin.menus.edit', $id));
    }

    protected function getIndex()
    {
        return $this->get(route('admin.menus.index'));
    }

    protected function destroy($id)
    {
        return $this->delete(route('admin.menus.destroy', $id));
    }

    public function testCreate()
    {
        $res = $this->get(route('admin.menus.create'));
        $res->assertStatus(200);
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
            'uri' => str_repeat('a', 51),
            'order' => 10000,
        ]);
        $res->assertJsonValidationErrors(['icon', 'uri', 'title', 'order']);

        // order min
        $res = $this->postStore([
            'order' => -10000,
        ]);
        $res->assertJsonValidationErrors(['order']);

        factory(AdminMenu::class)->create();
        // parent_id exists
        $res = $this->postStore([
            'parent_id' => 999,
        ]);
        $res->assertJsonValidationErrors('parent_id');
    }

    public function testStore()
    {
        factory(AdminMenu::class)->create();
        $inputs = factory(AdminMenu::class)->make([
            'parent_id' => 1,
            'uri' => 'no/start/slash',
        ])->toArray();
        $inputs['created_at'] = (string) now()->addDay();

        $res = $this->postStore($inputs);
        $res->assertStatus(201);

        $this->assertDatabaseHas(
            'admin_menus',
            array_merge($inputs, [
                'created_at' => (string) now(),
                'id' => 2,
                'parent_id' => 1,
                'uri' => '/'.$inputs['uri'],
            ])
        );
    }

    public function testUpdateValidation()
    {
        // 与 store 唯一不同的是，parent_id 不能是自己
        factory(AdminMenu::class, 2)->create();
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
        factory(AdminMenu::class, 2)->create();

        $inputs = [
            'parent_id' => 2,
            'title' => 'new title',
            'icon' => 'new icon',
            'uri' => 'new/uri',
            'order' => 99,
        ];
        $res = $this->putUpdate(1, $inputs);
        $res->assertStatus(200);

        $inputs['uri'] = '/'.$inputs['uri'];
        $this->assertDatabaseHas('admin_menus', ['id' => 1] + $inputs);
    }

    public function testEdit()
    {
        $this->getEdit(999)->assertStatus(404);

        $menu = factory(AdminMenu::class)->create()->toArray();
        $res = $this->getEdit(1);
        $res->assertStatus(200)
            ->assertJsonFragment($menu);
    }

    public function testIndex()
    {
        app(\AdminMenusTableSeeder::class)->run();
        // 手动查出 3 级嵌套菜单
        $menu = AdminMenu::with(['children', 'children.children'])->find(2);
        $menu = $menu->toArray();

        $res = $this->getIndex();
        $res->assertStatus(200)
            ->assertJsonFragment($menu);
    }

    public function testDestroy()
    {
        $this->delete(999)->assertStatus(404);

        app(\AdminMenusTableSeeder::class)->run();
        $menu = AdminMenu::with(['children', 'children.children'])->find(2);

        $this->destroy($menu->id)->assertStatus(204);

        // 子菜单全部删除
        $this->assertDatabaseMissing('admin_menus', ['id' => $menu->id]);
        // hack 一下，无妨，，，
        $this->assertDatabaseMissing('admin_menus', ['id' => $menu['children'][0]['id']]);
        $this->assertDatabaseMissing('admin_menus', ['id' => $menu['children'][0]['children'][0]['id']]);
    }
}
