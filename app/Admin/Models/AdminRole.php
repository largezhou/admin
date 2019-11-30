<?php

namespace App\Admin\Models;

class AdminRole extends Model
{
    protected $fillable = ['name', 'slug'];

    public function permissions()
    {
        return $this->belongsToMany(
            AdminPermission::class,
            'admin_role_permission',
            'role_id',
            'permission_id'
        );
    }

    public function delete()
    {
        $this->permissions()->detach();
        return parent::delete();
    }
}
