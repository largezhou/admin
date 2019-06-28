<?php

namespace Tests\Feature;

use App\Models\SystemMediaCategory;
use Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\RequestActions;

class SystemMediaCategoryControllerTest extends AdminTestCase
{
    use RefreshDatabase;
    use RequestActions;
    protected $resourceName = 'system-media-categories';

    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    protected function createNestedData()
    {
        SystemMediaCategory::insert([
            [
                'id' => 1,
                'parent_id' => 0,
                'name' => 'level 0-1',
            ],
            [
                'id' => 2,
                'parent_id' => 0,
                'name' => 'level 0-2',
            ],
            [
                'id' => 3,
                'parent_id' => 1,
                'name' => 'level 1-1',
            ],
            [
                'id' => 4,
                'parent_id' => 3,
                'name' => 'level 3-1',
            ],
        ]);
    }

    public function testStoreValidation()
    {
        $this->createNestedData();

        // parent_id exists
        $res = $this->storeResource([
            'parent_id' => 111,
            'name' => 'level 0-1',
        ]);
        $res->assertJsonValidationErrors(['parent_id'])
            ->assertJsonMissingValidationErrors(['name']);

        // name 同级 unique
        $res = $this->storeResource([
            'parent_id' => 3,
            'name' => 'level 3-1',
        ]);
        $res->assertJsonValidationErrors(['name']);

        // parent_id 0
        // name 不同级重复
        $res = $this->storeResource([
            'parent_id' => 0,
            'name' => 'level 3-1',
        ]);
        $res->assertJsonMissingValidationErrors(['parent_id', 'name']);
    }

    public function testStore()
    {
        $res = $this->storeResource([
            'name' => 'level 0-1',
        ]);
        $res->assertStatus(201);
        $this->assertDatabaseHas('system_media_categories', [
            'id' => 1,
            'name' => 'level 0-1',
            'parent_id' => 0,
        ]);

        $res = $this->storeResource([
            'parent_id' => 1,
            'name' => 'level 1-1',
        ]);
        $res->assertStatus(201);

        $this->assertDatabaseHas('system_media_categories', [
            'id' => 2,
            'parent_id' => 1,
            'name' => 'level 1-1',
        ]);
    }

    public function testUpdate()
    {
        factory(SystemMediaCategory::class)->create(['name' => 'level 0-1']);
        factory(SystemMediaCategory::class)->create(['name' => 'level 0-2']);

        $res = $this->updateResource(1, [
            'parent_id' => 2,
            'name' => 'level 0-2',
        ]);
        $res->assertStatus(201);

        $res = $this->updateResource(1);
        $res->assertStatus(201);

        $this->assertDatabaseHas('system_media_categories', [
            'id' => 1,
            'parent_id' => 2,
            'name' => 'level 0-2',
        ]);
    }

    public function testEdit()
    {
        factory(SystemMediaCategory::class)->create(['name' => 'level 0-1']);

        $res = $this->editResource(1);
        $res->assertStatus(200)
            ->assertJson([
                'id' => 1,
                'name' => 'level 0-1',
                'parent_id' => 0,
            ]);
    }

    public function testDestroy()
    {
        $this->createNestedData();

        $res = $this->destroyResource(1);
        $res->assertStatus(204);

        $this->assertDatabaseMissing('system_media_categories', ['id' => 1]);
        $this->assertDatabaseMissing('system_media_categories', ['id' => 3]);
        $this->assertDatabaseMissing('system_media_categories', ['id' => 4]);

        $this->assertDatabaseHas('system_media_categories', ['id' => 2]);
    }

    public function testIndex()
    {
        $this->createNestedData();

        $res = $this->getResources();
        $res->assertStatus(200)
            ->assertJson([
                [
                    'id' => 1,
                    'parent_id' => 0,
                    'name' => 'level 0-1',
                    'children' => [
                        [
                            'id' => 3,
                            'parent_id' => 1,
                            'name' => 'level 1-1',
                            'children' => [
                                [
                                    'id' => 4,
                                    'parent_id' => 3,
                                    'name' => 'level 3-1',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => 2,
                    'parent_id' => 0,
                    'name' => 'level 0-2',
                ],
            ]);

        $res = $this->getResources(['except' => 1]);
        $res->assertStatus(200)
            ->assertJson([
                [
                    'id' => 2,
                    'parent_id' => 0,
                    'name' => 'level 0-2',
                ],
            ]);
    }
}
