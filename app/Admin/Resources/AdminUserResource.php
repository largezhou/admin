<?php

namespace App\Admin\Resources;

/**
 * @mixin \App\Admin\Models\AdminUser
 */
class AdminUserResource extends JsonResource
{
    public const FOR_INFO = 'info';
    public const FOR_EDIT_INFO = 'edit_info';
    public const FOR_EDIT = 'edit';
    public const FOR_INDEX = 'index';

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'avatar' => $this->avatar,
            $this->mergeFor(static::FOR_INFO, function () {
                return [
                    'roles' => $this->roles()->pluck('slug'),
                    'permissions' => $this->allPermissions()->pluck('slug'),
                ];
            }),
            $this->mergeFor(static::FOR_EDIT, function () {
                return [
                    'roles' => $this->roles()->pluck('id'),
                    'permissions' => $this->permissions()->pluck('id'),
                ];
            }),
            $this->mergeFor(static::FOR_EDIT_INFO, function () {
                return [
                    'roles' => $this->roles()->pluck('name'),
                    'permissions' => $this->permissions()->pluck('name'),
                ];
            }),
            $this->mergeFor(static::FOR_INDEX, function () {
                return [
                    'roles' => $this->roles->pluck('name'),
                    'permissions' => $this->permissions->pluck('name'),
                ];
            }),
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
