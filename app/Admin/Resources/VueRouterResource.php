<?php

namespace App\Admin\Resources;

use App\Admin\Traits\ResourceRolePermissionHelpers;
use App\Admin\Models\VueRouter;

class VueRouterResource extends JsonResource
{
    use ResourceRolePermissionHelpers;

    public function toArray($request)
    {
        /** @var VueRouter $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'parent_id' => $model->parent_id,
            'title' => $model->title,
            'icon' => $model->icon,
            'path' => $model->path,
            'order' => $model->order,
            'cache' => $model->cache,
            'menu' => $model->menu,
            'roles' => $this->when(
                $this->onlyRolePermissionIds,
                $model->roles()->pluck('id'),
                AdminRoleResource::collection($this->whenLoaded('roles'))
            ),
            'permission' => $model->permission,
            'created_at' => (string) $model->created_at,
            'updated_at' => (string) $model->updated_at,
        ];
    }
}
