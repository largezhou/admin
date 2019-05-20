<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\AdminUserResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminUserController extends Controller
{
    public function user()
    {
        $user = $this->guard()->user();
        return AdminUserResource::make($user);
    }
}
