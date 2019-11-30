<?php

namespace App\Admin\Requests;

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
                'bail',
                'required',
                'max:20',
                Rule::unique('system_media_categories', 'name')
                    ->where(function (Builder $query) use ($cate) {
                        // 有传 parent_id，即同时修改名称和 parent_id，
                        // 所以以传入的 parent_id 为准
                        // 没有传，则已当前分类的 parent_id 为准
                        $parentId = $this->input('parent_id') ?? optional($cate)->parent_id;
                        return $query->where('parent_id', $parentId);
                    })
                    ->ignore($id),
            ],
            'parent_id' => 'exists:system_media_categories,id',
        ];

        if ($this->isMethod('put')) {
            $keys = $this->keys();
            if ($this->onlyUpdateParentId()) {
                $keys[] = 'name';
            }

            $rules = Arr::only($rules, $keys);
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

    protected function onlyUpdateParentId()
    {
        return $this->isMethod('put') &&
            ($this->has('parent_id') && !$this->has('name'));
    }

    public function validationData()
    {
        $data = parent::validationData();

        if ($this->onlyUpdateParentId()) {
            $data['name'] = $this->route('system_media_category')->name;
        }

        return $data;
    }
}
