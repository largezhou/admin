<?php

namespace App\Admin\Requests;

use App\Admin\Models\Config;
use Illuminate\Foundation\Http\FormRequest;

/**
 * 仅更新配置的值时的验证请求，从配置中收集配置的验证规则来进行验证
 *
 * @package App\Admin\Requests
 */
class UpdateConfigValuesRequest extends FormRequest
{
    /**
     * @var Config[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $configs;

    protected $validationRules = [];
    protected $validationAttributes = [];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->gatherRulesAndAttributes();

        return $this->validationRules;
    }

    public function getConfigs()
    {
        if (!$this->configs) {
            $this->configs = Config::query()
                ->whereIn('slug', $this->keys())
                ->get();
        }

        return $this->configs;
    }

    /**
     * 从数据库中收集本次请求要更新的字段的 name 和 validation_rules
     */
    protected function gatherRulesAndAttributes()
    {
        $configs = $this->getConfigs();

        $rules = [];
        $attributes = [];
        foreach ($configs as $config) {
            /** @var Config $config */
            $rules[$config->slug] = $config->validation_rules ?: 'nullable';
            $attributes[$config->slug] = $config->name;
        }

        $this->validationRules = $rules;
        $this->validationAttributes = $attributes;
    }

    public function attributes()
    {
        return $this->validationAttributes;
    }
}
