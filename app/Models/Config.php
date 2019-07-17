<?php

namespace App\Models;

class Config extends Model
{
    const TYPE_INPUT = 'input';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_FILE = 'file';
    public static $typeMap = [
        self::TYPE_INPUT => '文本',
        self::TYPE_TEXTAREA => '多行文本',
        self::TYPE_FILE => '文件',
    ];
    protected $fillable = [
        'category_id', 'type', 'name', 'slug', 'desc', 'options', 'value', 'validation_rules',
    ];
    protected $casts = [
        'category_id' => 'integer',
        'options' => 'array',
        'value' => 'array',
    ];

    public function getTypeTextAttribute()
    {
        return static::$typeMap[$this->type] ?? '';
    }

    public function category()
    {
        return $this->belongsTo(ConfigCategory::class);
    }
}
