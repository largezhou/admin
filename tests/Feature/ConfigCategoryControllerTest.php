<?php

namespace Tests\Feature;

use App\Models\Config;
use App\Models\ConfigCategory;
use Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\RequestActions;

class ConfigCategoryControllerTest extends AdminTestCase
{
    use RefreshDatabase;
    use RequestActions;
    protected $resourceName = 'config-categories';

    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    public function testStoreValidation()
    {
        // name required
        $res = $this->storeResource([
            'name' => '',
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        // name string
        $res = $this->storeResource([
            'name' => [],
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        // name max:50
        $res = $this->storeResource([
            'name' => str_repeat('a', 51),
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        factory(ConfigCategory::class)->create(['name' => 'name']);
        // name unique
        $res = $this->storeResource([
            'name' => 'name',
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function testStore()
    {
        $res = $this->storeResource([
            'name' => 'name',
        ]);
        $res->assertStatus(201);
        $this->assertDatabaseHas('config_categories', [
            'id' => 1,
            'name' => 'name',
        ]);
    }

    public function testUpdate()
    {
        factory(ConfigCategory::class)->create(['name' => 'name']);
        $res = $this->updateResource(1, [
            'name' => 'name',
        ]);
        $res->assertStatus(201);

        $res = $this->updateResource(1, [
            'name' => 'new',
        ]);
        $res->assertStatus(201);
        $this->assertDatabaseHas('config_categories', [
            'id' => 1,
            'name' => 'new',
        ]);
    }

    public function testDestroy()
    {
        factory(ConfigCategory::class, 2)->create();
        ConfigCategory::find(2)->configs()->createMany([factory(Config::class)->make()->toArray()]);

        $res = $this->destroyResource(1);
        $res->assertStatus(204);
        $this->assertDatabaseMissing('config_categories', [
            'id' => 1,
        ]);
        $this->assertDatabaseHas('configs', [
            'id' => 1,
            'category_id' => 2,
        ]);

        // 关联删除
        $res = $this->destroyResource(2);
        $res->assertStatus(204);
        $this->assertDatabaseMissing('config_categories', [
            'id' => 2,
        ]);
        $this->assertDatabaseMissing('configs', [
            'id' => 1,
        ]);
    }

    public function testIndex()
    {
        ConfigCategory::insert(factory(ConfigCategory::class, 20)->make()->toArray());
        ConfigCategory::find(1)->update(['name' => 'test query name']);
        ConfigCategory::find(2)->update(['name' => 'test query name 2']);

        $res = $this->getResources();
        $res->assertStatus(200)
            ->assertJsonCount(15, 'data');

        // name like %?%
        $res = $this->getResources([
            'name' => 'query',
        ]);
        $res->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
}
