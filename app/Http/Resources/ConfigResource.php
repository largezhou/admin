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

        if ($model->type == Config::TYPE_FILE) {
            $storage = Storage::disk('uploads');

            if (Arr::get($model->options, 'max', 1) > 1) {
                $model->value = array_map(function ($i) use ($storage) {
                    return [
                        'path' => $i,
                        'url' => $storage->url($i),
                    ];
                }, $model->value);
            } elseif ($model->value) {
                $model->value = [
                    'path' => $model->value,
                    'url' => $storage->url($model->value),
                ];
            }
        }

        return array_merge(parent::toArray($request), $data);
    }
}
