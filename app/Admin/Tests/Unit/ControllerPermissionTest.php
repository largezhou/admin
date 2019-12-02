<?php

namespace App\Admin\Tests\Unit;

use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use Illuminate\Support\Facades\Route;
use App\Admin\Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Admin\Tests\Traits\RequestActions;

class ControllerPermissionTest extends AdminTestCase
{
    use RefreshDatabase;
    use RequestActions;
    protected $resourceName = 'test-resources';

    protected function setUp(): void
    {
        parent::setUp();

        $this->login();

        $this->checkPermission(true);

        $this->prepare();
    }

    protected function prepare()
    {
        Route::prefix('admin-api')
            ->middleware([
                'admin',
                'admin.auth',
                'admin.permission',
            ])
            ->as('admin.')
            ->group(function () {
                Route::middleware('admin.permission:check,with-args')
                    ->group(function () {
                        Route::get('test-resources/with-args', 'App\Admin\Tests\Controllers\DummyAdminController@withArgs');
                    });

                Route::get('test-resources/check', 'App\Admin\Tests\Controllers\DummyAdminController@check');
                Route::get('test-resources/pass-through', 'App\Admin\Tests\Controllers\DummyAdminController@passThrough');
                Route::resource('test-resources', 'App\Admin\Tests\Controllers\DummyAdminController');
            });
    }

    protected function bindRole($role = [], $permissions = [])
    {
        $permissions = array_map(function ($attributes) {
            return factory(AdminPermission::class)->create($attributes);
        }, $permissions);

        $role = factory(AdminRole::class)->create($role);
        $role->permissions()->attach(collect($permissions)->pluck('id'));

        $this->user->roles()->attach($role->id);
    }

    protected function bindPermission($attributes = [])
    {
        $this->user->permissions()->create(factory(AdminPermission::class)->create($attributes)->toArray());
    }

    public function testNoPermission()
    {
        $res = $this->getResources();
        $res->assertStatus(403);
    }

    public function testExactMethodAndPath()
    {
        $this->bindPermission([
            'http_method' => ['GET'],
            'http_path' => '/test-resources',
        ]);
        $this->user->permissions()->attach(1);
        $res = $this->getResources();
        $res->assertStatus(200);
    }

    public function testAnyMethod()
    {
        $this->bindPermission([
            'http_method' => [],
            'http_path' => '/test-resources',
        ]);
        $res = $this->getResources();
        $res->assertStatus(200);
        $res = $this->storeResource();
        $res->assertStatus(201);
    }

    public function testSpecifyMethodInPath()
    {
        $this->bindPermission([
            'http_method' => ['GET'],
            'http_path' => 'POST:/test-resources',
        ]);
        $res = $this->storeResource();
        $res->assertStatus(201);
    }

    public function testIsAdministrator()
    {
        $this->bindRole([
            'slug' => 'administrator',
        ], [
            [
                'slug' => '*',
                'http_method' => [],
                'http_path' => '*',
            ],
        ]);

        $res = $this->getResources();
        $res->assertStatus(200);
        $res = $this->storeResource();
        $res->assertStatus(201);
    }

    public function testPermissionsInRole()
    {
        $this->bindRole([], [
            [
                'http_method' => ['GET'],
                'http_path' => '/test-resources',
            ],
            [
                'http_method' => ['POST'],
                'http_path' => '/test-resources',
            ],
        ]);

        $res = $this->getResources();
        $res->assertStatus(200);
        $res = $this->storeResource();
        $res->assertStatus(201);
    }

    public function testPermissionCheckInMethod()
    {
        $this->bindPermission([
            'http_path' => '',
            'http_method' => '',
            'slug' => 'check',
        ]);

        $res = $this->get('/admin-api/test-resources/check');
        $res->assertStatus(200);
    }

    public function testPermissionCheckWithArgs()
    {
        $this->bindPermission([
            'slug' => 'with-args',
        ]);

        $res = $this->get('/admin-api/test-resources/with-args');
        $res->assertStatus(200);
    }

    public function testUseUrlWhitelist()
    {
        $res = $this->get('/admin-api/test-resources/pass-through');
        $res->assertStatus(200);
    }
}
