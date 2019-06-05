<?php

namespace App\Http\Resources;


class JsonResource extends \Illuminate\Http\Resources\Json\JsonResource
{
    public static function collection($resource)
    {
        return new ResourceCollection($resource, static::class);
    }
}
