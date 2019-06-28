<?php

namespace Tests\Feature;

use App\Http\Controllers\Controller;
use App\Models\SystemMediaCategory;
use Illuminate\Http\UploadedFile;
use Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\RequestActions;

class SystemMediaControllerTest extends AdminTestCase
{
    use RequestActions;
    use RefreshDatabase;
    protected $resourceName = 'system-media';

    protected function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    public function testDestroy()
    {
        factory(SystemMediaCategory::class)->create();
        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $this->storeResource(
            [
                'file' => $file,
                Controller::UPLOAD_FOLDER_FIELD => 'tests',
            ],
            'system-media-categories.system-media',
            ['id' => 1]
        );

        $fileFullPath = public_path(Controller::UPLOAD_FOLDER_PREFIX.'/tests/'.md5_file($file).'.jpg');
        $this->assertFileExists($fileFullPath);

        $res = $this->destroyResource(1);
        $res->assertStatus(204);

        $this->assertFileNotExists($fileFullPath);
        $this->assertDatabaseMissing('system_media', [
            'id' => 1,
        ]);
    }
}
