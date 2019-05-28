<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminBaseController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function guard()
    {
        return auth('admin-api');
    }

    /**
     * 由于只需要返回一个响应，并附带一个 Vue-Component 头，
     * 这里大部分时候都不需要逻辑
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        return $this->ok();
    }
}
