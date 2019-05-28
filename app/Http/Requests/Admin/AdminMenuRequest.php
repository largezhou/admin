<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminMenuRequest extends FormRequest
{
    public function rules()
    {
        $rules = [];
        $parentIdExists = Rule::exists('admin_menus', 'id');

        switch (strtolower($this->method())) {
            case 'post':
                $rules['parent_id'] = $parentIdExists;
                break;
            case 'put':
                $rules['parent_id'] = $parentIdExists->whereNot('id', $this->route('menu')->id);
                break;
        }

        $rules = array_merge($rules, [
            'title' => 'required|max:50',
            'icon' => 'max:50',
            'uri' => 'max:50',
            'order' => 'integer|between:-9999,9999',
        ]);

        return $rules;
    }
}
