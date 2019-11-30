<?php

namespace App\Admin\Traits;

use App\Admin\Resources\AdminPermissionResource;
use App\Admin\Resources\AdminRoleResource;
use App\Admin\Models\AdminUser;

trait ResourceRolePermissionHelpers
{
    /**
     * 关联的角色和权限, 是否只是 id 数组
     *
     * @var bool
     */
    protected $onlyRolePermissionIds = false;
    /**
     * 关联的角色和权限，只包含 slug
     *
     * @var bool
     */
    protected $onlyRolePermissionSlugs = false;
    /**
     * 获取所有权限，包括角色中的
     *
     * @var bool
     */
    protected $gatherAllPermissions = false;

    public function onlyRolePermissionIds($yes = true)
    {
        $this->onlyRolePermissionIds = $yes;
        return $this;
    }

    public function onlyRolePermissionSlugs($yes = true)
    {
        $this->onlyRolePermissionSlugs = $yes;
        return $this;
    }

    public function gatherAllPermissions($yes = true)
    {
        $this->gatherAllPermissions = $yes;
        return $this;
    }

    protected function getRoles()
    {
        /** @var AdminUser $model */
        $model = $this->resource;

        if ($this->onlyRolePermissionIds) {
            return $model->roles()->pluck('id');
        } elseif ($this->onlyRolePermissionSlugs) {
            return $model->roles()->pluck('slug');
        } else {
            return AdminRoleResource::collection($this->whenLoaded('roles'));
        }
    }

    protected function getPermissions()
    {
        /** @var AdminUser $model */
        $model = $this->resource;

        if ($this->gatherAllPermissions) {
            $perms = $model->allPermissions();
        } else {
            $perms = $model->permissions();
        }

        if ($this->onlyRolePermissionIds) {
            return $perms->pluck('id');
        } elseif ($this->onlyRolePermissionSlugs) {
            return $perms->pluck('slug');
        } elseif ($this->gatherAllPermissions) {
            return AdminPermissionResource::collection($perms);
        } else {
            return AdminPermissionResource::collection($this->whenLoaded('permissions'));
        }
    }
}
