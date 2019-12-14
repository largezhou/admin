<?php

namespace App\Admin\Tests\Feature;

use App\Admin\Tests\AdminTestCase;
use Illuminate\Support\Facades\Storage;

class ResourceMakeCommandTest extends AdminTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = Storage::fake('local');
        $this->app->instance('path', storage_path('framework/testing/disks/local/app'));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testMake()
    {
        $this
            ->artisan('admin:make-resource', [
                'name' => 'admin-banner',
            ])
            ->assertExitCode(1);

        $files = [
            'Models/AdminBanner.php',
            'Filters/AdminBannerFilter.php',
            'Requests/AdminBannerRequest.php',
            'Resources/AdminBannerResource.php',
            'Controllers/AdminBannerController.php',
        ];

        foreach ($files as $file) {
            $this->assertFileExists($this->storage->path('app/Admin/'.$file));
        }
        $this->assertFileNotExists($this->storage->path('app/Admin/Tests/Feature/AdminBannerControllerTest.php'));

        $controllerContent = file_get_contents($this->storage->path('app/Admin/Controllers/AdminBannerController.php'));

        $this->assertEquals(substr_count($controllerContent, 'AdminBannerResource'), 5);
        $this->assertEquals(substr_count($controllerContent, 'AdminBannerRequest'), 3);
        $this->assertEquals(substr_count($controllerContent, 'AdminBannerFilter'), 2);
    }

    public function testSpecifyModelAndWithTest()
    {
        $this
            ->artisan('admin:make-resource', [
                'name' => 'admin-banner',
                '--model' => '\\App\\Models\\AdminBanner',
                '--test' => true,
            ])
            ->assertExitCode(0);

        $this->storage->put(
            'app/Models/AdminBanner.php',
            '<?php namespace App\Models; class AdminBanner {}'
        );
        include_once $this->storage->path('app/Models/AdminBanner.php');

        $this
            ->artisan('admin:make-resource', [
                'name' => 'admin-banner',
                '--model' => '\\App\\Models\\AdminBanner',
                '--test' => true,
            ])
            ->assertExitCode(1);

        $this->assertFileExists($this->storage->path('app/Admin/Tests/Feature/AdminBannerControllerTest.php'));
    }

    public function testFileExists()
    {
        $this->storage->put('app/Admin/Models/AdminBanner.php', '');
        $this
            ->artisan('admin:make-resource', [
                'name' => 'admin-banner',
            ])
            ->assertExitCode(0);
        $this
            ->artisan('admin:make-resource', [
                'name' => 'admin-banner',
                '--force' => true,
            ])
            ->assertExitCode(1);
    }
}
