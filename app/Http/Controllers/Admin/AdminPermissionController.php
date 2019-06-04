<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminPermissionRequest;
use App\Models\Admin\AdminPermission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminPermissionController extends Controller
{
    public function store(AdminPermissionRequest $request)
    {
        $inputs = $request->validated();
        AdminPermission::create($inputs);
        return $this->created();
    }
}
