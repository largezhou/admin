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
}
