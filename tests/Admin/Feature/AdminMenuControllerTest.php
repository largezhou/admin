<?php

namespace Tests\Admin\Feature;

use App\Http\Resources\AdminMenuResource;
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

    public function testCreate()
    {
        $res = $this->get(route('admin.menus.create'));
        $res->assertStatus(200);
    }

    public function testStoreValidation()
    {
        // title required
        // order integer
        $res = $this->postStore([
            'title' => '',
            'order' => 15.1,
        ]);
        $res->assertJsonValidationErrors(['title', 'order']);
        $res = $this->postStore([
            'title' => 'title',
            'order' => 15,
        ]);
        $res->assertJsonMissingValidationErrors(['title', 'order']);

        // max
        $res = $this->postStore([
            'title' => str_repeat('a', 51),
            'icon' => str_repeat('a', 51),
            'uri' => str_repeat('a', 51),
            'order' => 10000,
        ]);
        $res->assertJsonValidationErrors(['icon', 'uri', 'title', 'order']);
        $res = $this->postStore([
            'title' => str_repeat('a', 3),
            'icon' => str_repeat('a', 3),
            'uri' => str_repeat('a', 3),
            'order' => 999,
        ]);
        $res->assertJsonMissingValidationErrors(['icon', 'uri', 'title', 'order']);

        // order min
        $res = $this->postStore([
            'order' => -10000,
        ]);
        $res->assertJsonValidationErrors(['order']);
        $res = $this->postStore([
            'order' => 0,
        ]);
        $res->assertJsonMissingValidationErrors(['order']);

        factory(AdminMenu::class)->create();
        // parent_id exists
        $res = $this->postStore([
            'parent_id' => 999,
        ]);
        $res->assertJsonValidationErrors('parent_id');
        $res = $this->postStore([
            'parent_id' => 1,
        ]);
        $res->assertJsonMissingValidationErrors('parent_id');
    }

    public function testStore()
    {
        factory(AdminMenu::class)->create();
        $inputs = factory(AdminMenu::class)->make([
            'parent_id' => 1,
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
        $this->createdNestedMenus();

        // 手动查出 3 级嵌套菜单
        $menu = AdminMenu::find(1);
        // assertJsonFragment 中，会对键进行排序，被处理后的数据，与原始数据顺序不对
        // 所有这里查的数据，对不对 order 排序，都不影响断言，，，
        $children = AdminMenu::where('parent_id', $menu->id)->get()->each(function ($i) {
            $children = AdminMenu::where('parent_id', $i->id)->get()->toArray();
            if (!empty($children)) {
                $i->children = $children;
            }
        })->toArray();
        if (!empty($children)) {
            $menu->children = $children;
        }
        $menu = $menu->toArray();

        $res = $this->getIndex();
        $res->assertStatus(200)
            ->assertJsonFragment($menu);
    }

    protected function createdNestedMenus()
    {
        $menus = factory(AdminMenu::class, 9)->make([
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();
        // 三级嵌套菜单
        foreach ($menus as $i => &$menu) {
            if ($i < 3) {
                $parentId = 0;
            } else {
                $parentId = $i - 2;
            }
            $menu['parent_id'] = $parentId;
        }
        unset($menu);

        AdminMenu::insert($menus);
    }
}
