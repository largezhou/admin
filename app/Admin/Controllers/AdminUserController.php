<?php

namespace App\Admin\Controllers;

use App\Admin\Filters\AdminUserFilter;
use App\Admin\Requests\AdminUserProfileRequest;
use App\Admin\Requests\AdminUserRequest;
use App\Admin\Resources\AdminUserResource;
use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use App\Admin\Models\AdminUser;
use App\Admin\Utils\Admin;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function user()
    {
        $user = Admin::user();
        return $this->ok(
            AdminUserResource::make($user)
                ->gatherAllPermissions()
                ->onlyRolePermissionSlugs()
        );
    }

    public function editUser()
    {
        $user = Admin::user();
        $user->load(['roles', 'permissions']);
        return $this->ok(AdminUserResource::make($user));
    }

    public function updateUser(AdminUserProfileRequest $request)
    {
        $inputs = $request->validated();
        Admin::user()->updateUser($inputs);
        return $this->callAction('user', [])->setStatusCode(201);
    }

    public function index(AdminUserFilter $filter)
    {
        $users = AdminUser::query()
            ->filter($filter)
            ->with(['roles', 'permissions'])
            ->orderByDesc('id')
            ->paginate();

        return $this->ok(AdminUserResource::collection($users));
    }

    public function store(AdminUserRequest $request, AdminUser $user)
    {
        $inputs = $request->validated();
        $user = $user::createUser($inputs);

        if (!empty($q = $request->post('roles', []))) {
            $user->roles()->attach($q);
        }
        if (!empty($q = $request->post('permissions', []))) {
            $user->permissions()->attach($q);
        }

        return $this->created(AdminUserResource::make($user));
    }

    public function show(AdminUser $adminUser)
    {
        $adminUser->load(['roles', 'permissions']);

        return $this->ok(AdminUserResource::make($adminUser));
    }

    public function update(AdminUserRequest $request, AdminUser $adminUser)
    {
        $inputs = $request->validated();
        $adminUser->updateUser($inputs);
        if (isset($inputs['roles'])) {
            $adminUser->roles()->sync($inputs['roles']);
        }
        if (isset($inputs['permissions'])) {
            $adminUser->permissions()->sync($inputs['permissions']);
        }
        return $this->created(AdminUserResource::make($adminUser));
    }

    public function destroy(AdminUser $adminUser)
    {
        $adminUser->delete();
        return $this->noContent();
    }

    public function edit(Request $request, AdminUser $adminUser)
    {
        $formData = $this->formData();

        $adminUser->load(['roles', 'permissions']);
        $adminUserData = AdminUserResource::make($adminUser)
            ->onlyRolePermissionIds()
            ->toArray($request);

        return $this->ok(array_merge($formData, [
            'admin_user' => $adminUserData,
        ]));
    }

    /**
     * 返回创建和编辑表单所需的选项数据
     *
     * @return array
     */
    protected function formData()
    {
        $roles = AdminRole::query()
            ->orderByDesc('id')
            ->get();
        $permissions = AdminPermission::query()
            ->orderByDesc('id')
            ->get();

        return compact('roles', 'permissions');
    }

    public function create()
    {
        return $this->ok($this->formData());
    }
}
