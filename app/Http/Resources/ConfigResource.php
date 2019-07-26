<?php

namespace App\Http\Resources;

use App\Models\Config;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ConfigResource extends JsonResource
{
    public function toArray($request)
    {
        /** @var Config $model */
        $model = $this->resource;

        $data = [
            'type_text' => $model->type_text,
            'category' => ConfigCategoryResource::make($this->whenLoaded('category')),
        ];

        $model->handleFileTypeValue();

        return array_merge(parent::toArray($request), $data);
    }
}
