<?php

namespace App\Admin\Controllers\Auth;

use App\Admin\Controllers\Controller;
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

    /**
     * @return \Illuminate\Contracts\Auth\Guard|\Tymon\JWTAuth\JWTGuard|\Tymon\JWTAuth\JWT
     */
    protected function guard()
    {
        return Admin::guard();
    }
}
