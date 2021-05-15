<?php

namespace App\Http\Resources;

/**
 * @mixin \App\Models\AdminRole
 */
class AdminRoleResource extends JsonResource
{
    public const FOR_INDEX = 'index';
    public const FOR_EDIT = 'edit';

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            $this->mergeFor(static::FOR_INDEX, function () {
                return [
                    'permissions' => $this->permissions->pluck('name'),
                ];
            }),
            $this->mergeFor(static::FOR_EDIT, function () {
                return [
                    'permissions' => $this->permissions()->select(['id', 'name'])->get(),
                ];
            }),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
