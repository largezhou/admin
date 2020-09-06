<?php

namespace App\Admin\Controllers\Auth;

use App\Admin\Controllers\Controller;
use App\Admin\Models\Config;
use App\Admin\Utils\Admin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function username()
    {
        return 'username';
    }

    protected function authenticated(Request $request, $user)
    {
        return $this->created();
    }

    public function logout()
    {
        $this->guard()->logout();
        return $this->noContent();
    }

    /**
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function guard()
    {
        return Admin::guard();
    }

    protected function validateLogin(Request $request)
    {
        $rules = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        if (config('admin.system_basic.admin_login_captcha', '1') === '1') {
            $rules['captcha'] = 'required|captcha_api:'.$request->input('key');
        }

        $request->validate($rules, ['captcha_api' => '验证码 错误。'], ['captcha' => '验证码']);
    }
}
