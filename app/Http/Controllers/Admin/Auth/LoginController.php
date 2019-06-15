<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\AdminBaseController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends AdminBaseController
{
    use AuthenticatesUsers;

    public function username()
    {
        return 'username';
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

    public function logout()
    {
        $this->guard()->logout();
        return $this->noContent();
    }
}
