<?php

namespace App\Admin\Resources;

use App\Admin\Models\SystemMedia;

class SystemMediaResource extends JsonResource
{
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data = array_merge($data, [
            'category' => $this->whenLoaded('category'),
        ]);

        return $data;
    }
}
