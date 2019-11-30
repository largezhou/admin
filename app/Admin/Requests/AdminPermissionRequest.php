<?php

namespace App\Admin\Requests;

use App\Admin\Models\AdminPermission;
use App\Admin\Rules\AdminPermissionHttpPath;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class AdminPermissionRequest extends FormRequest
{
    public function rules()
    {
        $id = (int) optional($this->route('admin_permission'))->id;
        $rules = [
            'name' => 'required|unique:admin_permissions,name,'.$id,
            'slug' => 'required|unique:admin_permissions,slug,'.$id,
            'http_method' => 'nullable|array',
            'http_method.*' => Rule::in(AdminPermission::$httpMethods),
            'http_path' => [
                'nullable',
                new AdminPermissionHttpPath(),
            ],
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
            'http_method' => '请求方法',
            'http_method.*' => '请求方法',
            'http_path' => '请求地址',
        ];
    }
}
