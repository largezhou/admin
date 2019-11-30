<?php

namespace App\Admin\Tests\Unit;

use App\Admin\Models\Model;
use Illuminate\Http\Request;
use App\Admin\Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BaseModelTest extends AdminTestCase
{
    public function testGetPerPage()
    {
        $model = app(Model::class);

        $this->mockGetPerPage('4');
        $this->assertEquals(4, $model->getPerPage());

        // 不规范测试
        foreach (['4.4', 'not number', '-1', '0'] as $page) {
            $this->mockGetPerPage($page);
            $this->assertEquals(15, $model->getPerPage());
        }

        // 大于最大每页数测试
        $model = new class() extends Model
        {
            public function getMaxPerPage()
            {
                return $this->maxPerPage;
            }
        };
        $maxPerPage = $model->getMaxPerPage();
        $this->mockGetPerPage((string) ($maxPerPage + 1));
        $this->assertEquals($maxPerPage, $model->getPerPage());
    }

    /**
     * 伪造请求的 get 返回
     *
     * @param $page
     */
    protected function mockGetPerPage($page)
    {
        $this->setUp();
        $request = new class($page) extends Request
        {
            public $mockPage;

            public function __construct($page)
            {
                parent::__construct();
                $this->mockPage = $page;
            }

            public function get($key, $default = null)
            {
                return $this->mockPage;
            }
        };

        $this->instance('request', $request);
    }
}
