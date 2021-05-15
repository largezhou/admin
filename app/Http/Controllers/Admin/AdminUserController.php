<?php

namespace App\Http\Controllers\Admin;

use App\Http\Filters\AdminUserFilter;
use App\Http\Requests\AdminUserProfileRequest;
use App\Http\Requests\AdminUserRequest;
use App\Http\Resources\AdminUserResource;
use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\AdminUser;
use App\Utils\Admin;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function user()
    {
        $user = Admin::user();
        return $this->ok(AdminUserResource::make($user)->for(AdminUserResource::FOR_INFO));
    }

    public function editUser()
    {
        $user = Admin::user();
        return $this->ok(AdminUserResource::make($user)->for(AdminUserResource::FOR_EDIT_INFO));
    }

    public function updateUser(AdminUserProfileRequest $request)
    {
        $inputs = $request->validated();
        Admin::user()->updateUser($inputs);
        return $this->created(AdminUserResource::make(Admin::user()));
    }

    public function index(AdminUserFilter $filter)
    {
        $users = AdminUser::query()
            ->filter($filter)
            ->with(['roles', 'permissions'])
            ->orderByDesc('id')
            ->paginate();

        return $this->ok(AdminUserResource::forCollection(AdminUserResource::FOR_INDEX, $users));
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

        return $this->created();
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
        return $this->created(AdminUserResource::make($adminUser)->for(AdminUserResource::FOR_EDIT));
    }

    public function destroy(AdminUser $adminUser)
    {
        $adminUser->delete();
        return $this->noContent();
    }

    public function edit(Request $request, AdminUser $adminUser)
    {
        return $this->ok(
            AdminUserResource::make($adminUser)
                ->for(AdminUserResource::FOR_EDIT)
                ->additional($this->formData())
        );
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
