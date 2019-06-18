<?php

namespace App\Http\Controllers\Admin;

use App\Filters\AdminUserFilter;
use App\Http\Requests\AdminUserRequest;
use App\Http\Resources\AdminUserResource;
use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;

class AdminUserController extends AdminBaseController
{
    public function user()
    {
        $user = $this->guard()->user();
        return $this->ok(AdminUserResource::make($user));
    }

    public function index(AdminUserFilter $filter)
    {
        $users = AdminUser::query()
            ->filter($filter)
            ->with([
                'roles' => function (BelongsToMany $query) {
                    $query->select(['id', 'name']);
                },
                'permissions' => function (BelongsToMany $query) {
                    $query->select(['id', 'name']);
                },
            ])
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
        $adminUser->load([
            'roles' => function (BelongsToMany $query) {
                $query->select(['id', 'name']);
            },
            'permissions' => function (BelongsToMany $query) {
                $query->select(['id', 'name']);
            },
        ]);

        return $this->ok(AdminUserResource::make($adminUser));
    }

    public function update(AdminUserRequest $request, AdminUser $adminUser)
    {
        $inputs = $request->validated();
        $adminUser->updateUser($inputs);
        if (!empty($q = $request->post('roles', []))) {
            $adminUser->roles()->sync($q);
        }
        if (!empty($q = $request->post('permissions', []))) {
            $adminUser->permissions()->sync($q);
        }
        return $this->created(AdminUserResource::make($adminUser));
    }

    public function destroy(AdminUser $adminUser)
    {
        $adminUser->delete();
        return $this->noContent();
    }
}
