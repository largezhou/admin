<?php

namespace App\Models;

use Illuminate\Support\Facades\Request;

class Model extends \Illuminate\Database\Eloquent\Model
{
    public function getPerPage()
    {
        $perPage = Request::get('per_page');
        $int = (int) $perPage;
        if (($int > 0) && ((string) $int === $perPage)) {
            return $int;
        } else {
            return $this->perPage;
        }
    }
}
