<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdminRoleRequest;
use App\Http\Resources\AdminRoleResource;
use App\Models\AdminRole;
use App\Utils\WhereBuilder;
use Illuminate\Database\Eloquent\Builder;
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
        $adminRole->load([
            'permissions' => function (BelongsToMany $query) {
                $query->select(['id', 'name']);
            },
        ]);
        return $this->ok(AdminRoleResource::make($adminRole));
    }

    public function update(AdminRoleRequest $request, AdminRole $adminRole)
    {
        $inputs = $request->validated();
        $adminRole->update($inputs);
        if (isset($inputs['permissions'])) {
            $adminRole->permissions()->sync($inputs['permissions']);
        }
        return $this->created(AdminRoleResource::make($adminRole));
    }

    public function destroy(AdminRole $adminRole)
    {
        $adminRole->delete();
        return $this->noContent();
    }

    public function index(Request $request, WhereBuilder $whereBuilder)
    {
        $where = $whereBuilder->setInputs($request->input())
            ->like(['name', 'slug'], '?%')
            ->toWhere();
        $roles = AdminRole::query()
            ->with([
                'permissions' => function (BelongsToMany $query) {
                    $query->select(['id', 'name']);
                },
            ])
            ->where($where)
            ->orderByDesc('id')
            ->paginate();

        return $this->ok(AdminRoleResource::collection($roles));
    }
}
