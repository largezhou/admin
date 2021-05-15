<?php

namespace App\Http\Resources;

class JsonResource extends \Illuminate\Http\Resources\Json\JsonResource
{
    public static $wrap = null;

    /**
     * @var string 自定义的场景，根据不同场景，来组合不同的返回字段
     */
    protected static $for;

    public static function collection($resource)
    {
        return new ResourceCollection($resource, static::class);
    }

    public function for(string $for)
    {
        static::$for = $for;

        return $this;
    }

    public static function forCollection(string $for, ...$parameters)
    {
        static::$for = $for;

        return static::collection(...$parameters);
    }

    public function mergeFor(string $for, ...$parameters)
    {
        return $this->mergeWhen(static::$for === $for, ...$parameters);
    }
}
