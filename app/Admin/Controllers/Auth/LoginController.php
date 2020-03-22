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

    protected function authenticated(Request $request, $user)
    {
        return $this->created();
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
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function guard()
    {
        return Admin::guard();
    }
}
