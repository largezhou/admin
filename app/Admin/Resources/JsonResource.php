<?php

namespace App\Admin\Resources;

class JsonResource extends \Illuminate\Http\Resources\Json\JsonResource
{
    public static $wrap = null;

    public static function collection($resource)
    {
        return new ResourceCollection($resource, static::class);
    }
}
