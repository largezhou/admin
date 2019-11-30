<?php

namespace App\Admin\Resources;

use App\Admin\Models\AdminRole;

class AdminRoleResource extends JsonResource
{
    public function toArray($request)
    {
        /** @var AdminRole $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'name' => $model->name,
            'slug' => $model->slug,
            'permissions' => AdminPermissionResource::collection($this->whenLoaded('permissions')),
            'created_at' => (string) $model->created_at,
            'updated_at' => (string) $model->updated_at,
        ];
    }
}
