<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:50|unique:config_categories,name,'.
                (int) $this->route()->originalParameter('config_category'),
        ];
    }

    public function attributes()
    {
        return [
            'name' => '名称',
        ];
    }
}
