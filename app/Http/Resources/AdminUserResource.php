<?php

namespace App\Http\Resources;

use App\Models\AdminRole;
use App\Models\AdminUser;
use App\Traits\ResourceRolePermissionHelpers;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
{
    use ResourceRolePermissionHelpers;

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
}
