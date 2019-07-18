<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class ConfigCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $cateId = (int) $this->route()->originalParameter('config_category');
        $rule = function ($field) use ($cateId) {
            return "required|string|max:50|unique:config_categories,{$field},".(int) $cateId;
        };
        $rules = [
            'name' => $rule('name'),
            'slug' => $rule('slug'),
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
        ];
    }
}
