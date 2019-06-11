<?php

namespace App\Rules;

use App\Models\Admin\AdminPermission;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class AdminPermissionHttpPath implements Rule
{
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
        $path = array_filter(explode("\r\n", $value));
        foreach ($path as $i) {
            if (Str::contains($i, ':')) {
                [$methods, $path] = explode(':', $i);
                if ($methods) {
                    $methods = explode(',', $methods);
                    if (!empty(array_diff($methods, AdminPermission::$httpMethods))) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'HTTP 路径 无效';
    }
}
