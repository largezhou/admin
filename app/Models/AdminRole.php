<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminRole extends Model
{
    protected $fillable = ['name', 'slug'];

    public function permissions()
    {
        return $this->belongsToMany(
            AdminPermission::class,
            'admin_permission_role',
            'role_id',
            'permission_id'
        )->withTimestamps();
    }

    public function delete()
    {
        $this->permissions()->detach();
        return parent::delete();
    }
}
