<?php

namespace App\Models;

class SystemMediaCategory extends Model
{
    protected $fillable = ['parent_id', 'name'];
    protected $casts = [
        'parent_id' => 'integer',
    ];

    public function media()
    {
        return $this->hasMany(SystemMedia::class, 'cate_id');
    }

    public function children()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function setParentIdAttribute($value)
    {
        $this->attributes['parent_id'] = $value ?: 0;
    }
}
