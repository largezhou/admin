<?php

namespace App\Models;

class SystemMedia extends Model
{
    protected $fillable = [
        'filename', 'ext', 'cate_id', 'path', 'size', 'mime_type',
    ];
    protected $casts = [
        'cate_id' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(SystemMediaCategory::class, 'cate_id');
    }
}
