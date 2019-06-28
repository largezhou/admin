<?php

namespace App\Models;

class SystemMedia extends Model
{
    protected $fillable = [
        'filename', 'ext', 'category_id', 'path', 'size', 'mime_type',
    ];
    protected $casts = [
        'category_id' => 'integer',
        'size' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(SystemMediaCategory::class, 'category_id');
    }
}
