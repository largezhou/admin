<?php

namespace App\Http\Resources;

use App\Models\AdminPermission;
use Illuminate\Support\Arr;

class AdminPermissionResource extends JsonResource
{
    public function toArray($request)
    {
        /** @var AdminPermission $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'name' => $model->name,
            'slug' => $model->slug,
            'http_method' => $model->http_method,
            'http_path' => $model->http_path,
            'created_at' => (string) $model->created_at,
            'updated_at' => (string) $model->updated_at,
        ];
    }
}
