<?php

namespace App\Admin\Resources;

/**
 * @mixin \App\Admin\Models\Config
 */
class ConfigResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'type_text' => $this->type_text,
            'category' => ConfigCategoryResource::make($this->whenLoaded('category')),
        ];

        return array_merge(parent::toArray($request), $data);
    }
}
