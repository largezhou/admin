<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class VueRouterRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'title' => 'required|max:50',
            'icon' => 'max:50',
            'path' => 'max:50',
            'order' => 'integer|between:-9999,9999',
            'cache' => 'boolean',
            'is_menu' => 'boolean',
        ];
        $parentIdExists = Rule::exists('vue_routers', 'id');

        switch (strtolower($this->method())) {
            case 'post':
                $rules['parent_id'] = $parentIdExists;
                break;
            case 'put':
                $rules['parent_id'] = $parentIdExists->whereNot('id', $this->route('vue_router')->id);
                $rules = Arr::only($rules, $this->keys());
                break;
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
            'is_menu' => '显示',
        ];
    }
}