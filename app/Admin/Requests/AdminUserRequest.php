<?php

namespace App\Admin\Requests;

use App\Admin\Models\AdminUser;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class AdminUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = $this->userResource();
        $id = (int) optional($user)->id;
        $rules = [
            'username' => 'required|max:100|unique:admin_users,username,'.$id,
            'name' => 'required|max:100',
            'avatar' => 'nullable|string|max:255',
            'password' => 'required|between:6,20|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:admin_roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:admin_permissions,id',
        ];
        if ($this->isMethod('put')) {
            $rules = Arr::only($rules, $this->keys());
            // 如果更新时, 没填密码, 则不用验证
            if (!$this->post('password')) {
                unset($rules['password']);
            }
            // 处理更新时，图片没有改，则不用验证
            if ($this->input('avatar') === Storage::disk('uploads')->url($user->avatar)) {
                unset($rules['avatar']);
            }
        }

        return $rules;
    }

    /**
     * @return AdminUser
     */
    public function userResource()
    {
        return $this->route('admin_user');
    }

    public function attributes()
    {
        return [
            'username' => '账号',
            'name' => '姓名',
            'password' => '密码',
            'roles' => '角色',
            'roles.*' => '角色',
            'permissions' => '权限',
            'permissions.*' => '权限',
            'avatar' => '头像',
        ];
    }
}
