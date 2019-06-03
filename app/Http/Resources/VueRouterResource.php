<?php

namespace App\Http\Resources;

use App\Models\Admin\VueRouter;
use Illuminate\Http\Resources\Json\JsonResource;

class VueRouterResource extends JsonResource
{
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
            'is_menu' => $model->is_menu,
            'created_at' => (string) $model->created_at,
            'updated_at' => (string) $model->updated_at,
        ];
    }
}
