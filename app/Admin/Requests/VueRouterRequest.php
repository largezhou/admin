<?php

namespace App\Admin\Requests;

use Illuminate\Support\Arr;

class VueRouterRequest extends FormRequest
{
    public function rules()
    {
        if ($this->route()->getName() == 'admin.vue-routers.by-import') {
            return [
                'file' => 'required|file',
            ];
        }

        $rules = [
            'title' => 'required|max:50',
            'icon' => 'max:50',
            'path' => 'max:50',
            'order' => 'integer|between:-9999,9999',
            'cache' => 'boolean',
            'menu' => 'boolean',
            'roles' => 'array',
            'roles.*' => 'exists:admin_roles,id',
            'permission' => 'nullable|exists:admin_permissions,slug',
            'parent_id' => 'exists:vue_routers,id',
        ];

        if ($this->isMethod('put')) {
            $rules = Arr::only($rules, $this->keys());
        }

        if ($this->post('parent_id') == 0) {
            $rules['parent_id'] = 'nullable';
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'parent_id' => '父级菜单',
            'title' => '标题',
            'icon' => '图标',
            'path' => '地址',
            'order' => '排序',
            'cache' => '缓存',
            'menu' => '显示',
            'roles' => '角色',
            'roles.*' => '角色',
            'permission' => '权限',
            'file' => '文件',
        ];
    }
}
