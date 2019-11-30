<?php

namespace App\Admin\Resources;

use App\Admin\Traits\ResourceRolePermissionHelpers;
use App\Admin\Models\AdminUser;

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
            'roles' => $this->getRoles(),
            'permissions' => $this->getPermissions(),
            'created_at' => (string) $model->created_at,
            'updated_at' => (string) $model->updated_at,
        ];
    }
}
