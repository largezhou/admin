<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class RewriteController extends Controller
{
    public function __invoke()
    {
        return view('admin'.(app()->environment('dev') ? '-dev' : ''));
    }
}
