<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminMenuRequest extends FormRequest
{
    public function rules()
    {
        return [
            'parent_id' => Rule::exists('admin_menus', 'id'),
            'title' => 'required|max:50',
            'icon' => 'max:50',
            'uri' => 'max:50',
            'order' => 'integer',
        ];
    }
}
