<?php

namespace App\Admin\Resources;

/**
 * @mixin \App\Admin\Models\VueRouter
 */
class VueRouterResource extends JsonResource
{
    public const FOR_EDIT = 'edit';

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'icon' => $this->icon,
            'path' => $this->path,
            'order' => $this->order,
            'cache' => $this->cache,
            'menu' => $this->menu,
            $this->mergeFor(static::FOR_EDIT, function () {
                return [
                    'roles' => $this->roles()->pluck('id'),
                ];
            }),
            'permission' => $this->permission,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
