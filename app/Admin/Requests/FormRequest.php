<?php

namespace App\Admin\Requests;

use Illuminate\Contracts\Validation\Validator;

class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * 是否把数组字段中元素的错误, 合并到数组字段的错误中,
     * 合并之后, 不会出现 field.* 之类的错误, 全部显示在 field 字段中
     *
     * @var bool
     */
    protected $mergeArrayError = true;

    protected function failedValidation(Validator $validator)
    {
        $this->mergeArrayError($validator);

        parent::failedValidation($validator);
    }

    /**
     * 合并数组字段中元素的错误, 到数组字段的错误中
     *
     * @param Validator $validator
     */
    protected function mergeArrayError(Validator $validator)
    {
        if (!$this->mergeArrayError) {
            return;
        }
        $msgBag = $validator->errors();
        foreach ($msgBag->messages() as $field => $errors) {
            // 取出第一个 . 号前面的, 作为字段名,
            // 例如 http_method.0 --> http_method
            // some.0.nested --> some
            $dotPos = strpos($field, '.');
            if ($dotPos === false) {
                continue;
            }

            $targetField = substr($field, 0, $dotPos);
            foreach ($errors as $i) {
                $msgBag->add($targetField, $i);
            }
        }
    }
}
