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
}
