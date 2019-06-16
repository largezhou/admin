<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdminRoleRequest;
use App\Http\Resources\AdminRoleResource;
use App\Models\AdminRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminRoleController extends Controller
{
    public function store(AdminRoleRequest $request, AdminRole $model)
    {
        $inputs = $request->validated();
        $role = $model->create($inputs);

        if (!empty($perms = $inputs['permissions'] ?? [])) {
            $role->permissions()->attach($perms);
        }

        return $this->created(AdminRoleResource::make($role));
    }

    public function edit(AdminRole $adminRole)
    {
        $adminRole->load(['permissions' => function (BelongsToMany $query) {
            $query->select(['id', 'name']);
        }]);
        return $this->ok(AdminRoleResource::make($adminRole));
    }
}
