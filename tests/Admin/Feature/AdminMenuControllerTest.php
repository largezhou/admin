<?php

namespace Tests\Admin\Feature;

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
        $res->assertJsonValidationErrors(['title', 'order'])
            ->assertJsonMissingValidationErrors(['icon', 'uri', 'parent_id']);

        // max 50
        $res = $this->postStore([
            'title' => str_repeat('a', 51),
            'icon' => str_repeat('a', 51),
            'uri' => str_repeat('a', 51),
        ]);
        $res->assertJsonValidationErrors(['icon', 'uri', 'title']);

        factory(AdminMenu::class)->create();
        // parent_id fail
        $res = $this->postStore([
            'parent_id' => 2,
        ]);
        $res->assertJsonValidationErrors('parent_id');

        // parent_id pass
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
}
