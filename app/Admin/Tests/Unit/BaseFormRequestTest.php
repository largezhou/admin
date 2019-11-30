<?php

namespace App\Admin\Tests\Unit;

use App\Admin\Requests\FormRequest;
use Illuminate\Validation\ValidationException;
use App\Admin\Tests\AdminTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BaseFormRequestTest extends AdminTestCase
{
    public function testMergeArrayError()
    {
        // 测试合并
        $request = $this->makeRequest(['array' => ['not integer']]);
        try {
            $request->validateResolved();
        } catch (ValidationException $e) {
            $this->assertTrue(key_exists('array', $e->validator->errors()->messages()));
        }

        // 测试不合并
        $request = $this->makeRequest(['array' => ['not integer']]);
        $request->setMergeArrayError(false);
        try {
            $request->validateResolved();
        } catch (ValidationException $e) {
            $this->assertFalse(key_exists('array', $e->validator->errors()->messages()));
        }
    }

    /**
     * 指定请求数据, 生成一个 表单请求 实例
     *
     * @param array $data
     *
     * @return DummyFormRequest
     */
    protected function makeRequest($data = [])
    {
        $request = new DummyFormRequest($data);
        app()->call([$request, 'setContainer']);
        app()->call([$request, 'setRedirector']);
        return $request;
    }
}

class DummyFormRequest extends FormRequest
{
    public function rules()
    {
        return [
            'array.*' => 'integer',
        ];
    }

    public function setMergeArrayError($merge)
    {
        $this->mergeArrayError = $merge;
    }
}
