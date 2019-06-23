<?php

namespace Tests\Feature;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\VueRouter;
use Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConfigControllerTest extends AdminTestCase
{
    use RefreshDatabase;

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
}
