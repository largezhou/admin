<?php

namespace App\Admin\Requests;

use App\Admin\Utils\Admin;
use Illuminate\Support\Arr;

class AdminUserProfileRequest extends AdminUserRequest
{
    public function rules()
    {
        $rules = Arr::only(parent::rules(), [
            'name', 'password', 'avatar',
        ]);
        return $rules;
    }

    public function userResource()
    {
        return Admin::user();
    }
}
