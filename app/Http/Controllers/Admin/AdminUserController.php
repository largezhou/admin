<?php

namespace App\Http\Controllers\Admin;

use App\Filters\AdminUserFilter;
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
}
