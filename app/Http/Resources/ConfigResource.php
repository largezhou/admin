<?php

namespace App\Http\Resources;

use App\Models\Config;

class ConfigResource extends JsonResource
{
    public function toArray($request)
    {
        /** @var Config $model */
        $model = $this->resource;

        $data = [
            'type_text' => $model->type_text,
        ];

        return array_merge(parent::toArray($request), $data);
    }
}
