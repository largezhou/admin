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
}
