<?php

namespace App\Http\Resources;

use App\Models\SystemMedia;

class SystemMediaResource extends JsonResource
{
    public function toArray($request)
    {
        /** @var SystemMedia $model */
        $model = $this->resource;

        $data = parent::toArray($request);
        $data = array_merge($data, [
            'category' => $this->whenLoaded('category'),
            'url' => url($model->path),
        ]);

        return $data;
    }
}
