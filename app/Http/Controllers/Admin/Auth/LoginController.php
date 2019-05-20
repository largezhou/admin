<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Traits\RestfulResponse;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;
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

    public function logout()
    {
        $this->guard()->logout();
        return $this->noContent();
    }
}
