<?php

namespace App\Admin\Rules;

use BadMethodCallException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ValidRules implements Rule
{
    protected $invalidRules = [];

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
        if (!$value || !is_string($value)) {
            return true;
        }

        foreach (explode('|', $value) as $rule) {
            $v = Validator::make(['field' => 'data'], ['field' => $rule]);
            try {
                $v->passes();
            } catch (BadMethodCallException $e) {
                $this->invalidRules[] = $rule;
            }
        }

        return empty($this->invalidRules);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $t = implode(', ', $this->invalidRules);
        return ":attribute [ {$t} ] 不存在。";
    }
}
