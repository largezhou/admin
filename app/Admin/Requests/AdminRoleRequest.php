<?php

namespace App\Admin\Requests;

use Illuminate\Support\Arr;

class AdminRoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = (int) optional($this->route('admin_role'))->id;
        $rules = [
            'name' => 'required|unique:admin_roles,name,'.$id,
            'slug' => 'required|unique:admin_roles,slug,'.$id,
            'permissions' => 'array',
            'permissions.*' => 'exists:admin_permissions,id',
        ];
        if ($this->isMethod('put')) {
            $rules = Arr::only($rules, $this->keys());
        }
        return $rules;
    }

    public function attributes()
    {
        return [
            'name' => '名称',
            'slug' => '标识',
            'permissions' => '权限',
            'permissions.*' => '权限',
        ];
    }
}
