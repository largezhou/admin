<?php

namespace App\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class ConfigCategoryRequest extends FormRequest
{
    /**
     * 一些预留的名字，不能设置为这些别名
     *
     * @var array
     */
    protected $reservedSlugs = [
        // 避免出现请求 /configs/test，在开发环境下，会定位到 TestSomethingController
        'test',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $cateId = (int) $this->route()->originalParameter('config_category');
        $rules = [
            'name' => 'required|string|max:50|unique:config_categories,name,'.$cateId,
            'slug' => 'required|string|max:50|not_in:'.implode(',', $this->reservedSlugs).'|unique:config_categories,slug,'.$cateId,
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

    public function messages()
    {
        return [
            'slug.not_in' => ':attribute 不能是：'.implode(',', $this->reservedSlugs).' 之一',
        ];
    }
}
