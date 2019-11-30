<?php

namespace App\Admin\Rules;

use App\Admin\Models\Config;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

/**
 * 单选或者多选类型时，选项配置验证规则
 * 必须至少要有一个类似 1=>值 的值
 *
 * Class ConfigSelectTypeOptions
 * @package App\Admin\Rules
 */
class ConfigSelectTypeOptions implements Rule
{
    protected $errorMessage;

    public function passes($attribute, $value)
    {
        // 为空不验证
        if (!$value) {
            return true;
        }

        if (!is_string($value)) {
            return false;
        }

        $pairs = explode("\n", $value);
        foreach ($pairs as $pair) {
            $p = explode('=>', $pair);
            if (count($p) < 2) {
                return false;
            }
            $label = $p[1];
            // label 有就表示有效，有一个有效，则可以通过
            if ($label) {
                return true;
            }
        }

        return false;
    }

    public function message()
    {
        return '选项设置 无效。';
    }
}

class ConfigOptions implements Rule
{
    /**
     * @var string 配置类型
     */
    protected $type;
    protected $errorMessage;

    /**
     * Create a new rule instance.
     *
     * @param string $type
     *
     * @return void
     */
    public function __construct(string $type = null)
    {
        $this->type = $type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // 如果 type 类型不对，则不用验证
        if (!isset(Config::$typeMap[$this->type])) {
            return true;
        }

        switch ($this->type) {
            case Config::TYPE_INPUT:
            case Config::TYPE_TEXTAREA:
            case Config::TYPE_OTHER:
                !is_null($value) && ($this->errorMessage = '选项 必须为空。');
                break;
            case Config::TYPE_FILE:
                if (!is_array($value)) {
                    $this->errorMessage = '选项 无效。';
                    break;
                }

                $validator = Validator::make($value, [
                    'max' => 'required|integer|between:1,99',
                    'ext' => 'nullable',
                ], [], [
                    'max' => '最大上传数',
                    'ext' => '文件类型',
                ]);
                break;
            case Config::TYPE_SINGLE_SELECT:
            case Config::TYPE_MULTIPLE_SELECT:
                if (!is_array($value)) {
                    $this->errorMessage = '选项 无效。';
                    break;
                }

                $validator = Validator::make($value, [
                    'options' => ['required', new ConfigSelectTypeOptions()],
                    'type' => 'required|in:input,select',
                ], [], [
                    'options' => '选项设置',
                    'type' => '选择形式',
                ]);
                break;
            default:
                // do nothing
        }

        if (isset($validator) && $validator->fails()) {
            $this->errorMessage = $validator->getMessageBag()->first();
        }

        return !$this->errorMessage;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage;
    }
}
