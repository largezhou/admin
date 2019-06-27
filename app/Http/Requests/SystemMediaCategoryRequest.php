<?php

namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class SystemMediaCategoryRequest extends FormRequest
{
    public function rules()
    {
        $cate = $this->route('system_media_category');
        $id = optional($cate)->id;

        $rules = [
            'name' => [
                'required',
                Rule::unique('system_media_categories', 'name')
                    ->where(function (Builder $query) {
                        return $query->where('parent_id', $this->input('parent_id'));
                    })
                    ->ignore($id),
            ],
            'parent_id' => 'exists:system_media_categories,id',
        ];

        if ($this->isMethod('put')) {
            $rules = Arr::only($rules, $this->keys());
        }

        if ($this->input('parent_id') == 0) {
            $rules['parent_id'] = 'nullable';
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'parent_id' => '父级分类',
            'name' => '名称',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => '同级下 :attribute 已经存在',
        ];
    }
}
