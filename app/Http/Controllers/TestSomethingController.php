<?php

namespace App\Http\Controllers;

class TestSomethingController extends Controller
{
    public function index($path = null)
    {
        dd($path);
    }
}
