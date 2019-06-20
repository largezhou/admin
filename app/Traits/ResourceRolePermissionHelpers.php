<?php

namespace App\Traits;

trait ResourceRolePermissionHelpers
{
    /**
     * 关联的角色和权限, 是否只是 id 数组
     *
     * @var bool
     */
    protected $onlyRolePermissionIds = false;

    public function onlyRolePermissionIds($yes = true)
    {
        $this->onlyRolePermissionIds = $yes;
        return $this;
    }
}
