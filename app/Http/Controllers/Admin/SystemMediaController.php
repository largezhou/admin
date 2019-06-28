<?php

namespace App\Http\Controllers\Admin;

use App\Models\SystemMedia;
use Illuminate\Http\Request;

class SystemMediaController extends AdminBaseController
{
    public function destroy(SystemMedia $systemMedia)
    {
        $systemMedia->delete();
        return $this->noContent();
    }
}
