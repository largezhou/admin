<?php

namespace App\Http\Resources;

use App\Models\AdminUser;
use App\Traits\ResourceRolePermissionHelpers;
use Illuminate\Support\Facades\Storage;

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
            'avatar' => $this->when(
                $model->avatar,
                Storage::disk('uploads')->url($model->avatar),
                null
            ),
            'roles' => $this->getRoles(),
            'permissions' => $this->getPermissions(),
            'created_at' => (string) $model->created_at,
            'updated_at' => (string) $model->updated_at,
        ];
    }
}
