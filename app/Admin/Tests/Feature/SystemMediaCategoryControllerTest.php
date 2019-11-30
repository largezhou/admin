<?php

namespace App\Admin\Tests\Feature;

use App\Admin\Controllers\Controller;
use App\Admin\Models\SystemMedia;
use App\Admin\Models\SystemMediaCategory;
use Illuminate\Http\UploadedFile;
use App\Admin\Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Admin\Tests\Traits\RequestActions;

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
        // name max:20
        $res = $this->storeResource([
            'parent_id' => 111,
            'name' => str_repeat('a', 21),
        ]);
        $res->assertJsonValidationErrors(['parent_id', 'name']);

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
        $id1 = $this->getLastInsertId('system_media_categories');
        $this->assertDatabaseHas('system_media_categories', [
            'id' => $id1,
            'name' => 'level 0-1',
            'parent_id' => 0,
        ]);

        $res = $this->storeResource([
            'parent_id' => $id1,
            'name' => 'level 1-1',
        ]);
        $res->assertStatus(201);
        $id2 = $this->getLastInsertId('system_media_categories');
        $this->assertDatabaseHas('system_media_categories', [
            'id' => $id2,
            'parent_id' => $id1,
            'name' => 'level 1-1',
        ]);
    }

    public function testUpdate()
    {
        $id1 = factory(SystemMediaCategory::class)->create(['name' => '分类'])->id;
        $id2 = factory(SystemMediaCategory::class)
            ->create([
                'name' => '分类', // 不同父级分类下的同名分类
                'parent_id' => $id1,
            ])->id;
        $id3 = factory(SystemMediaCategory::class)
            ->create([
                'name' => '又一个分类',
                'parent_id' => $id1,
            ])->id;

        // name 同级 unique
        $res = $this->updateResource($id2, [
            'name' => '又一个分类',
        ]);
        $res->assertJsonValidationErrors(['name']);

        // 只修改父级时，判断 name 的 unique
        $res = $this->updateResource($id2, [
            'parent_id' => 0,
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        $res = $this->updateResource($id2, [
            'parent_id' => $id3,
            'name' => '修改分类和父级',
        ]);
        $res->assertStatus(201);

        $this->assertDatabaseHas('system_media_categories', [
            'id' => $id2,
            'parent_id' => $id3,
            'name' => '修改分类和父级',
        ]);

        $res = $this->updateResource($id2);
        $res->assertStatus(201);
    }

    public function testEdit()
    {
        $id = factory(SystemMediaCategory::class)->create(['name' => 'level 0-1'])->id;

        $res = $this->editResource($id);
        $res->assertStatus(200)
            ->assertJson([
                'id' => $id,
                'name' => 'level 0-1',
                'parent_id' => 0,
            ]);
    }

    public function testDestroy()
    {
        $this->createNestedData();
        factory(SystemMedia::class)->create(['category_id' => 3]);

        $res = $this->destroyResource(1);
        $res->assertStatus(204);

        // 删除所有子孙分类
        $this->assertDatabaseMissing('system_media_categories', ['id' => 1]);
        $this->assertDatabaseMissing('system_media_categories', ['id' => 3]);
        $this->assertDatabaseMissing('system_media_categories', ['id' => 4]);

        // 被删除的分类下的文件的分类 id 设为 0
        $this->assertDatabaseHas('system_media', [
            'id' => 1,
            'category_id' => 0,
        ]);

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

    /**
     * @param int $cateId
     * @param array $data
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function storeSystemMedia($cateId, $data = [])
    {
        return $this->storeResource(
            $data,
            $this->resourceName.'.system-media',
            ['system_media_category' => $cateId]
        );
    }

    public function testStoreSystemMedia()
    {
        $categoryId = factory(SystemMediaCategory::class)->create()->id;

        // file required
        $res = $this->storeSystemMedia($categoryId);
        $res->assertJsonValidationErrors(['file']);

        // file file
        $res = $this->storeSystemMedia($categoryId, ['file' => 'not a file']);
        $res->assertJsonValidationErrors(['file']);

        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $res = $this->storeSystemMedia($categoryId, [
            'file' => $file,
            Controller::UPLOAD_FOLDER_FIELD => 'tests',
        ]);
        $res->assertStatus(201);

        $filename = md5_file($file).'.jpg';
        $path = Controller::UPLOAD_FOLDER_PREFIX.'/tests/'.$filename;
        $this->assertDatabaseHas('system_media', [
            'id' => $this->getLastInsertId('system_media'),
            'category_id' => $categoryId,
            'filename' => $filename,
            'size' => $file->getSize(),
            'ext' => 'jpg',
            'mime_type' => $file->getMimeType(),
            'path' => $path,
        ]);
        $this->storage->exists($path);
        $this->storage->delete($path);
    }

    protected function systemMediaIndex($params = [])
    {
        return $this->getResources($params, $this->resourceName.'.system-media');
    }

    public function testSystemMediaIndex()
    {
        factory(SystemMediaCategory::class)
            ->create()
            ->media()
            ->createMany([
                factory(SystemMedia::class)->make([
                    'filename' => 'avatar.jpg',
                    'ext' => 'jpg',
                ])->toArray(),
                factory(SystemMedia::class)->make([
                    'filename' => 'funny.gif',
                    'ext' => 'gif',
                ])->toArray(),
            ]);
        $categoryId1 = $this->getLastInsertId('system_media_categories');

        // 其他分类的图片
        factory(SystemMediaCategory::class)
            ->create()
            ->media()
            ->createMany(factory(SystemMedia::class, 2)->make(['ext' => 'jpg'])->toArray());

        // ext in 筛选
        $res = $this->systemMediaIndex([
            'system_media_category' => $categoryId1,
            'ext' => ['jpg'],
        ]);
        $res->assertStatus(200)
            ->assertJsonFragment(['ext' => 'jpg'])
            ->assertJsonMissing(['ext' => 'gif']);

        $res = $this->systemMediaIndex([
            'system_media_category' => $categoryId1,
            'ext' => 'jpg,gif',
        ]);
        $res->assertStatus(200)
            ->assertJsonCount(2, 'data');

        // filename like
        $res = $this->systemMediaIndex([
            'system_media_category' => $categoryId1,
            'filename' => 'ny',
        ]);
        $res->assertStatus(200)
            ->assertJsonFragment(['filename' => 'funny.gif'])
            ->assertJsonMissing(['filename' => 'avatar.jpg']);
    }
}
