<?php

namespace App\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SystemMediaRequest extends FormRequest
{
    public function rules()
    {
        if ($this->isMethod('post')) { // 添加时，验证文件
            // 最大 10M，大文件之后再看
            $maxSize = 10 * 1024;
            return [
                'file' => 'required|file|max:'.$maxSize,
            ];
        } elseif ($this->isMethod('put')) { // 更新时，验证分类
            return [
                'category_id' => 'exists:system_media_categories,id',
            ];
        } else {
            return [];
        }
    }

    public function attributes()
    {
        return [
            'file' => '文件',
            'category_id' => '分类',
        ];
    }
}
