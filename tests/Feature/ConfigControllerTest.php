<?php

namespace Tests\Feature;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\Config;
use App\Models\ConfigCategory;
use App\Models\VueRouter;
use Illuminate\Support\Arr;
use Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\RequestActions;

class ConfigControllerTest extends AdminTestCase
{
    use RefreshDatabase;
    use RequestActions;
    protected $resourceName = 'configs';

    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    protected function getConfig(string $config)
    {
        return $this->get(route("admin.configs.{$config}"));
    }

    protected function prepareVueRouters()
    {
        factory(VueRouter::class, 5)->create();
        VueRouter::find(2)->children()->save(VueRouter::find(3));
        VueRouter::find(4)->children()->save(VueRouter::find(5));
    }

    public function testVueRoutersWithoutAuth()
    {
        $this->prepareVueRouters();

        $res = $this->getConfig('vue-routers');
        $res->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function testVueRoutersUserNoAuth()
    {
        $this->prepareVueRouters();

        // 绑定角色
        VueRouter::find(1)->roles()->create(
            factory(AdminRole::class)->create(['slug' => 'role-router-1'])->toArray()
        );
        // 子菜单绑定权限
        VueRouter::find(3)->update([
            'permission' => factory(AdminPermission::class)->create(['slug' => 'perm-router-3'])->slug,
        ]);
        $res = $this->getConfig('vue-routers');
        $res->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonMissing(['id' => 3]);
    }

    public function testVueRoutersUserHasAuth()
    {
        $this->prepareVueRouters();

        $this->user->roles()->attach(1);
        $this->user->permissions()->attach(1);

        $res = $this->getConfig('vue-routers');
        $res->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function testDestroy()
    {
        factory(Config::class)->create();

        $res = $this->destroyResource(1);
        $res->assertStatus(204);

        $this->assertDatabaseMissing('configs', ['id' => 1]);
    }

    public function testEdit()
    {
        factory(Config::class)->create();

        $res = $this->editResource(1);
        $res->assertStatus(200);
    }

    public function testUpdate()
    {
        factory(ConfigCategory::class)->create();
        factory(Config::class)->create([
            'name' => 'name',
            'slug' => 'slug',
            'type' => Config::TYPE_INPUT,
        ]);

        // category_id exists
        $res = $this->updateResource(1, [
            'category_id' => -1,
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);

        // name unique 排除自身
        $res = $this->updateResource(1, [
            'name' => 'name1',
        ]);
        $res->assertStatus(201)
            ->assertJsonMissingValidationErrors(['name']);

        // type, slug 不能修改
        $inputs = [
            'name' => 'new name',
            'type' => 'new type',
            'slug' => 'new slug',
            'category_id' => 1,
            'desc' => 'new desc',
            'options' => 'new options',
            'value' => 'new value',
            'validation_rules' => 'new rules',
        ];
        $res = $this->updateResource(1, $inputs);
        $res->assertStatus(201);

        $expectData = array_merge($inputs, [
            'type' => Config::TYPE_INPUT,
            'slug' => 'slug',
            'options' => json_encode('new options'),
            'value' => json_encode('new value'),
        ]);
        $this->assertDatabaseHas('configs', $expectData);
    }

    public function testIndex()
    {
        factory(ConfigCategory::class, 2)->create()
            ->each(function (ConfigCategory $cate) {
                $cate->configs()->createMany(factory(Config::class, 2)->make()->toArray());
            });

        $res = $this->getResources();
        $res->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }
}
