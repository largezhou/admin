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

    /**
     * 从请求数据中添加用户
     *
     * @param array $inputs
     * @param bool $hashedPassword 传入的密码, 是否是没有哈希处理的明文密码
     *
     * @return AdminUser|\Illuminate\Database\Eloquent\Model
     */
    public static function createUser($inputs, $hashedPassword = false)
    {
        if (!$hashedPassword) {
            $inputs['password'] = bcrypt($inputs['password']);
        }

        return static::create($inputs);
    }

    /**
     * 从请求数据中, 更新一条记录
     *
     * @param array $inputs
     * @param bool $hashedPassword 传入的密码, 是否是没有哈希处理的明文密码
     *
     * @return bool
     */
    public function updateUser($inputs, $hashedPassword = false)
    {
        // 密码有变化, 且没有经过哈希处理
        if (
            ($this->password != $inputs['password']) &&
            !$hashedPassword
        ) {
            $inputs['password'] = bcrypt($inputs['password']);
        }

        return $this->update($inputs);
    }
}
