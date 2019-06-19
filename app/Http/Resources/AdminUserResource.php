<?php

namespace App\Http\Resources;

use App\Models\AdminRole;
use App\Models\AdminUser;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
{
    /**
     * 关联的角色和权限, 是否只是 id 数组
     *
     * @var bool
     */
    protected $onlyRolePermissionIds = false;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        /** @var AdminUser $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'username' => $model->username,
            'name' => $model->name,
            'avatar' => $model->avatar,
            'roles' => $this->when(
                $this->onlyRolePermissionIds,
                $model->roles()->pluck('id'),
                AdminRoleResource::collection($this->whenLoaded('roles'))
            ),
            'permissions' => $this->when(
                $this->onlyRolePermissionIds,
                $model->permissions()->pluck('id'),
                AdminPermissionResource::collection($this->whenLoaded('permissions'))
            ),
            'created_at' => (string) $model->created_at,
            'updated_at' => (string) $model->updated_at,
        ];
    }

    public function onlyRolePermissionIds($yes = true)
    {
        $this->onlyRolePermissionIds = $yes;
        return $this;
    }
}
