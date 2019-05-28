<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\AdminUserResource;
use Illuminate\Http\Request;

class AdminUserController extends AdminBaseController
{
    public function user()
    {
        $user = $this->guard()->user();
        return $this->ok(AdminUserResource::make($user));
    }
}
