<?php

namespace App\Http\Requests;

use App\Models\Config;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class ConfigRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $configId = $this->route()->originalParameter('config');

        $rules = [
            'type' => 'required|in:'.implode(',', array_keys(Config::$typeMap)),
            'name' => 'required|string|max:50|unique:configs,name,'.(int) $configId,
            'slug' => 'required|string|max:50|unique:configs,slug,'.(int) $configId,
            'desc' => 'nullable|string|max:255',
            'options' => 'nullable|string|max:255',
            'value' => 'nullable|string|max:5000',
            'validation_rules' => 'nullable|string|max:255',
        ];

        if ($this->isMethod('put')) {
            $rules = Arr::except($rules, ['type', 'slug']);
            $rules['category_id'] = 'exists:config_categories,id';

            $rules = Arr::only($rules, $this->keys());
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'type' => '输入类型',
            'name' => '名称',
            'slug' => '字段名',
            'desc' => '描述',
            'options' => '配置',
            'value' => '值',
            'validation_rules' => '验证规则',
        ];
    }
}
