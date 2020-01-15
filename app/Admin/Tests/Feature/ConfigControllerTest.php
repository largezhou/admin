<?php

namespace App\Admin\Tests\Feature;

use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use App\Admin\Models\Config;
use App\Admin\Models\ConfigCategory;
use App\Admin\Models\VueRouter;
use App\Admin\Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Admin\Tests\Traits\RequestActions;

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

    /**
     * 构建一个简单的嵌套路由
     * [
     *     ['id' => 1],
     *     [
     *         'id' => 2,
     *         'children' => ['id' => 3],
     *     ],
     *     [
     *         'id' => 4,
     *         'children' => ['id' => 5],
     *     ],
     * ]
     *
     * @return array $ids
     */
    protected function prepareVueRouters()
    {
        $ids = factory(VueRouter::class, 5)->create()->pluck('id');
        VueRouter::find($ids[1])->children()->save(VueRouter::find($ids[2]));
        VueRouter::find($ids[3])->children()->save(VueRouter::find($ids[4]));

        return $ids;
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
        $ids = $this->prepareVueRouters();

        // 绑定角色
        VueRouter::find($ids[0])->roles()->create(
            factory(AdminRole::class)->create(['slug' => 'role-router-1'])->toArray()
        );
        // 子菜单绑定权限
        VueRouter::find($ids[2])->update([
            'permission' => factory(AdminPermission::class)->create(['slug' => 'perm-router-3'])->slug,
        ]);
        $res = $this->getConfig('vue-routers');
        $res->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonMissing(['id' => $ids[2]]);
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
        $id = factory(Config::class)->create()->id;

        $res = $this->editResource($id);
        $res->assertStatus(200);
    }

    public function testUpdate()
    {
        $categoryId = factory(ConfigCategory::class)->create()->id;
        $configId = factory(Config::class)->create([
            'name' => 'name',
            'slug' => 'slug',
            'type' => Config::TYPE_INPUT,
        ])->id;

        // category_id exists
        $res = $this->updateResource($configId, [
            'category_id' => -1,
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);

        // name unique 排除自身
        $res = $this->updateResource($configId, [
            'name' => 'name1',
        ]);
        $res->assertStatus(201)
            ->assertJsonMissingValidationErrors(['name']);

        // type, slug 不能修改
        $inputs = [
            'name' => 'new name',
            'type' => Config::TYPE_TEXTAREA,
            'slug' => 'new_slug',
            'category_id' => $categoryId,
            'desc' => 'new desc',
            'value' => 'new value',
            'validation_rules' => 'required',
        ];
        $res = $this->updateResource($configId, $inputs);
        $res->assertStatus(201);

        $expectData = array_merge($inputs, [
            'type' => Config::TYPE_TEXTAREA,
            'slug' => 'new_slug',
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

    public function testStoreValidation()
    {
        factory(ConfigCategory::class)->create();
        factory(Config::class)->create([
            'name' => 'name',
            'slug' => 'slug',
        ]);

        // type, name, slug required
        // category_id required
        // desc, validation_rules string
        $res = $this->storeResource([
            'desc' => [],
            'validation_rules' => [],
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors([
                'type', 'name', 'slug', 'desc',
                'validation_rules', 'category_id',
            ]);

        // type in
        // category_id exists
        // name, slug string
        // desc, validation_rules max:xx
        $res = $this->storeResource([
            'type' => 'not in',
            'category_id' => '-999',
            'name' => [],
            'slug' => [],
            'desc' => str_repeat('a', 256),
            'validation_rules' => str_repeat('a', 256),
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors([
                'type', 'name', 'slug', 'desc', 'validation_rules',
            ]);

        // name, slug max:50
        // validation_rules ValidRules
        $res = $this->storeResource([
            'type' => Config::TYPE_INPUT,
            'name' => str_repeat('a', 51),
            'slug' => str_repeat('a', 51),
            'validation_rules' => 'required|invalid_rule1|string|invalid_rule2',
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug', 'validation_rules'])
            ->assertSeeText('invalid_rule1, invalid_rule2');

        // name, slug unique
        $res = $this->storeResource([
            'type' => Config::TYPE_INPUT,
            'name' => 'name',
            'slug' => 'slug',
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug']);
    }

    public function testConfigOptionsValidation()
    {
        // type 字段无效时，不验证
        $res = $this->storeResource([
            'options' => null,
        ]);
        $res->assertJsonMissingValidationErrors(['options']);

        // options 只能为空的类型
        foreach ([Config::TYPE_INPUT, Config::TYPE_TEXTAREA, Config::TYPE_OTHER] as $type) {
            $res = $this->storeResource([
                'type' => $type,
                'options' => 'not null',
            ]);
            $res->assertJsonValidationErrors(['options']);
        }

        /**
         * type TYPE_FILE 的数据
         * [
         *     'max' => 'required|between:1,99',
         *     'ext' => 'nullable',
         * ]
         */
        $optionsInputs = [null, 'not number', '-1'];
        foreach ($optionsInputs as $max) {
            $res = $this->storeResource([
                'type' => Config::TYPE_FILE,
                'options' => [
                    'max' => $max,
                    'ext' => null,
                ],
            ]);
            $res->assertStatus(422)
                ->assertJsonValidationErrors('options');
        }

        /**
         * type TYPE_SINGLE_SELECT 或 TYPE_MULTIPLE_SELECT 的数据
         * [
         *     'options' => 'required|ConfigSelectTypeOptions',
         *     'type' => 'required|in:input,select',
         * ]
         */
        foreach ([null, [], "=>\n=>"] as $options) {
            $res = $this->storeResource([
                'type' => Config::TYPE_SINGLE_SELECT,
                'options' => [
                    'options' => $options,
                    'type' => 'input',
                ],
            ]);
            $res->assertStatus(422)
                ->assertJsonValidationErrors('options');
        }

        foreach ([null, 'not in'] as $type) {
            $res = $this->storeResource([
                'type' => Config::TYPE_SINGLE_SELECT,
                'options' => [
                    'options' => '1=>value1',
                    'type' => $type,
                ],
            ]);
            $res->assertStatus(422)
                ->assertJsonValidationErrors('options');
        }
    }

    public function testStore()
    {
        $categoryId = factory(ConfigCategory::class)->create()->id;

        $inputs = factory(Config::class)->make()->toArray();
        $inputs['category_id'] = $categoryId;

        $res = $this->storeResource($inputs);
        $res->assertStatus(201);

        $this->assertDatabaseHas('configs', [
            'id' => $this->getLastInsertId('configs'),
            'category_id' => $categoryId,
        ]);
    }

    public function testGetByCategorySlug()
    {
        $category = factory(ConfigCategory::class)->create();
        factory(Config::class, 2)->create(['category_id' => $category->id]);

        $res = $this->get(route('admin.configs.by-category-slug', [
            'category_slug' => $category->slug,
        ]));
        $res->assertStatus(200)
            ->assertJsonCount(2);

        $res = $this->get(route('admin.configs.by-category-slug', [
            'category_slug' => 'not exists slug',
        ]));
        $res->assertStatus(200)
            ->assertJsonCount(0);
    }

    public function testUpdateValues()
    {
        $category = factory(ConfigCategory::class)->create();
        factory(Config::class)->create([
            'slug' => 'field',
            'validation_rules' => 'required|max:20',
            'category_id' => $category->id,
        ]);

        $res = $this->put(route('admin.configs.update-values'), [
            'field' => null,
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['field']);

        $res = $this->put(route('admin.configs.update-values'), [
            'field' => 'new value',
        ]);
        $res->assertStatus(201);
        $this->assertDatabaseHas('configs', [
            'slug' => 'field',
            'value' => json_encode('new value'),
        ]);
    }

    public function testGetValuesByCategorySlug()
    {
        $category = factory(ConfigCategory::class)->create(['slug' => 'slug']);
        factory(Config::class)->create([
            'slug' => 'field',
            'type' => Config::TYPE_FILE,
            'category_id' => $category->id,

            'value' => 'uploads/test/logo.png',
        ]);

        $res = $this->get(route('admin.configs.values.by-category-slug', [
            'category_slug' => 'slug',
        ]));
        $res->assertStatus(200)
            ->assertJson([
                'field' => 'uploads/test/logo.png',
            ]);
    }

    public function testCreate()
    {
        factory(ConfigCategory::class)->create(['slug' => 'slug']);

        $res = $this->createResource();
        $res->assertStatus(200)
            ->assertJsonFragment([
                'slug' => 'slug',
                'types_map' => Config::$typeMap,
            ]);
    }
}
