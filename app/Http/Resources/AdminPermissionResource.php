<?php

namespace App\Http\Resources;

/**
 * @mixin \App\Models\AdminPermission
 */
class AdminPermissionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'http_method' => $this->http_method,
            'http_path' => $this->http_path,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
