<?php

namespace App\Models;

use App\Traits\ModelHelpers;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AdminUser extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use ModelHelpers;
    protected $fillable = ['username', 'password', 'name', 'avatar'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function roles()
    {
        return $this->belongsToMany(
            AdminRole::class,
            'admin_user_role',
            'user_id',
            'role_id'
        );
    }

    public function permissions()
    {
        return $this->belongsToMany(
            AdminPermission::class,
            'admin_user_permission',
            'user_id',
            'permission_id'
        );
    }
}
