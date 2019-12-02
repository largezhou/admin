<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    protected function getIndex(bool $isDev = false): string
    {
        $folder = $isDev ? 'admin-dev' : 'admin';
        $path = public_path("{$folder}/index.html");
        if (!file_exists($path)) {
            abort(404);
        }

        return file_get_contents($path);
    }

    public function index()
    {
        return $this->getIndex();
    }

    public function indexDev()
    {
        return $this->getIndex(true);
    }
}
