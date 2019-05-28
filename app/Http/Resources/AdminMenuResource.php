<?php

namespace App\Http\Resources;

use App\Models\Admin\AdminMenu;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminMenuResource extends JsonResource
{
    public function toArray($request)
    {
        /** @var AdminMenu $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'parent_id' => $model->parent_id,
            'title' => $model->title,
            'icon' => $model->icon,
            'uri' => $model->uri,
            'order' => $model->order,
            'created_at' => (string) $model->created_at,
            'updated_at' => (string) $model->updated_at,
        ];
    }
}
