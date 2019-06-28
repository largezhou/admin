<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VueRouter;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function vueRouters(VueRouter $vueRouter)
    {
        return $this->ok($vueRouter->treeWithAuth()->toTree());
    }
}
