<?php

namespace App\Traits;

trait RestfulResponse
{
    /**
     * 返回 201 已创建 响应
     *
     * @param null|string|array $data
     * @param array             $headers
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    protected function created($data = null, array $headers = [])
    {
        return response($data, 201, $headers);
    }

    /**
     * 返回 204 无内容 响应
     *
     * @param array $headers
     *
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    protected function noContent(array $headers = [])
    {
        return response(null, 204, $headers);
    }
}
