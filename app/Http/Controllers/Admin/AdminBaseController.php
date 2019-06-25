<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Utils\Admin;

class AdminBaseController extends Controller
{
    protected $uploadFolder = 'admin';

    /**
     * @return \Illuminate\Contracts\Auth\Guard|\Tymon\JWTAuth\JWTGuard
     */
    protected function guard()
    {
        return Admin::guard();
    }
}
