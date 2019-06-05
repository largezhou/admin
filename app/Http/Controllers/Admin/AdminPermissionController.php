<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminPermissionRequest;
use App\Http\Resources\AdminPermissionResource;
use App\Models\Admin\AdminPermission;
use App\Utils\WhereBuilder;
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

    public function index(Request $request, WhereBuilder $whereBuilder)
    {
        $where = $whereBuilder
            ->setInputs($request->query())
            ->equal('id')
            ->like(['slug', 'name'], '?%')
            ->like(['http_method', 'http_path'], '%?%')
            ->toWhere();

        $perms = AdminPermission::getQuery()
            ->where($where)
            ->orderByDesc('id')
            ->paginate();

        return $this->ok(AdminPermissionResource::collection($perms));
    }
}
