<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AdminMenu extends Model
{
    protected $fillable = ['parent_id', 'order', 'title', 'icon', 'uri'];
}
