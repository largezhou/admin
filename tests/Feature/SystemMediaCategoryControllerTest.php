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

    public function testStoreValidation()
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
        factory(SystemMediaCategory::class)->create();

        $res = $this->storeResource([
            'parent_id' => 1,
            'name' => 'level 1-1',
        ]);
        dump(json_decode($res->getContent()));
        $res->assertStatus(201);

        $this->assertDatabaseHas('system_media_categories', [
            'id' => 2,
            'parent_id' => 1,
            'name' => 'level 1-1',
        ]);
    }
}
