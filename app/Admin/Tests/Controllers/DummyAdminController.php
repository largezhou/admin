<?php

namespace App\Admin\Tests\Controllers;

use App\Admin\Controllers\Controller;
use App\Admin\Utils\PermissionChecker;

class DummyAdminController extends Controller
{
    public function index()
    {
        return $this->ok();
    }

    public function store()
    {
        return $this->created();
    }

    public function check()
    {
        PermissionChecker::check('check');
        return $this->ok();
    }

    public function withArgs()
    {
        return $this->ok();
    }

    public function passThrough()
    {
        return $this->ok();
    }
}
