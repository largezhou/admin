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
     * @return \Illuminate\Http\JsonResponse
     */
    protected function created($data = null, array $headers = [])
    {
        return response()->json($data, 201, $headers);
    }

    /**
     * 返回 204 无内容 响应
     *
     * @param array $headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function noContent(array $headers = [])
    {
        return response()->json(null, 204, $headers);
    }

    /**
     * 返回 200 OK 响应
     *
     * @param mixed $data
     * @param array $headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function ok($data = null, array $headers = [])
    {
        return response()->json($data, 200, $headers);
    }
}
