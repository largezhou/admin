<?php

namespace App\Http\Requests\Admin;

use App\Models\Admin\AdminPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class AdminPermissionRequest extends FormRequest
{
    public function rules()
    {
        $id = (int) optional($this->route('admin_permission'))->id;
        $rules = [
            'name' => 'required|unique:admin_permissions,name,'.$id,
            'slug' => 'required|unique:admin_permissions,slug,'.$id,
            'http_method' => 'in:'.implode(',', AdminPermission::$httpMethods),
            'http_path' => 'nullable',
        ];
        if ($this->isMethod('put')) {
            $rules = Arr::only($rules, $this->keys());
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name' => '名称',
            'slug' => '标识',
            'http_method' => '请求方法',
            'http_path' => '请求地址',
        ];
    }
}
