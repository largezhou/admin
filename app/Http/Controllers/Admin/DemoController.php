<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DemoController extends Controller
{
    public function resetSystem()
    {
        Artisan::call('admin:init', [
            '--force' => true,
        ]);
        return $this->ok();
    }
}
