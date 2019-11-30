<?php

namespace App\Admin\Controllers;

use App\Admin\Filters\AdminRoleFilter;
use App\Admin\Requests\AdminRoleRequest;
use App\Admin\Resources\AdminRoleResource;
use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use Illuminate\Http\Request;

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

    public function edit(Request $request, AdminRole $adminRole)
    {
        $adminRole->load(['permissions']);
        $roleData = AdminRoleResource::make($adminRole)->toArray($request);

        return $this->ok([
            'role' => $roleData,
            'permissions' => $this->formData()['permissions'],
        ]);
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

    public function index(Request $request, AdminRoleFilter $filter)
    {
        $roles = AdminRole::query()
            ->with(['permissions'])
            ->filter($filter)
            ->orderByDesc('id');
        $roles = $request->get('all') ? $roles->get() : $roles->paginate();

        return $this->ok(AdminRoleResource::collection($roles));
    }

    /**
     * 返回添加和编辑表单所需的选项数据
     *
     * @return array
     */
    protected function formData()
    {
        $permissions = AdminPermission::query()
            ->orderByDesc('id')
            ->get();

        return compact('permissions');
    }

    public function create()
    {
        return $this->ok($this->formData());
    }
}
