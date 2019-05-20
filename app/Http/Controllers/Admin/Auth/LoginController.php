<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Traits\RestfulResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends \App\Http\Controllers\Auth\LoginController
{
    use RestfulResponse;

    public function username()
    {
        return 'username';
    }

    public function guard()
    {
        return Auth::guard('admin_api');
    }

    protected function sendLoginResponse(Request $request)
    {
        return $this->created([
            'token' => $this->guard()->getToken()->get(),
            'token_type' => 'bearer',
            'expired_in' => $this->guard()->factory()->getTTL() * 60,
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt($this->credentials($request));
    }
}
