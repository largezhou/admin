<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AdminMenu extends Model
{
    protected $casts = [
        'parent_id' => 'integer',
        'order' => 'integer',
    ];

    protected $fillable = ['parent_id', 'order', 'title', 'icon', 'uri'];
}
