<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    protected function getIndex(string $path, bool $isDev = false): string
    {
        $folder = $path.($isDev ? '-dev' : '');
        $path = public_path("{$folder}/index.html");
        if (!file_exists($path)) {
            abort(404);
        }

        return file_get_contents($path);
    }

    public function index()
    {
        return $this->getIndex('admin');
    }

    public function indexDev()
    {
        return $this->getIndex('admin', true);
    }
}
