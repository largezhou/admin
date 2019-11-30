<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Builder;

class Config extends Model
{
    const TYPE_INPUT = 'input';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_FILE = 'file';
    const TYPE_SINGLE_SELECT = 'single_select';
    const TYPE_MULTIPLE_SELECT = 'multiple_select';
    const TYPE_OTHER = 'other';
    public static $typeMap = [
        self::TYPE_INPUT => '文本',
        self::TYPE_TEXTAREA => '多行文本',
        self::TYPE_FILE => '文件',
        self::TYPE_SINGLE_SELECT => '单选',
        self::TYPE_MULTIPLE_SELECT => '多选',
        self::TYPE_OTHER => '其他',
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

    /**
     * 通过配置分类标识，获取所有配置
     *
     * @param string $categorySlug
     * @param bool $onlyValues
     *
     * @return Config[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public static function getByCategorySlug(string $categorySlug, bool $onlyValues = false)
    {
        $configs = static::whereHas('category', function (Builder $query) use ($categorySlug) {
            $query->where('slug', $categorySlug);
        })->get();

        if ($onlyValues) {
            return $configs->pluck('value', 'slug');
        } else {
            return $configs;
        }
    }

    /**
     * @param Config[]|\Illuminate\Database\Eloquent\Collection $configs
     * @param array $inputs slug => value 键值对
     *
     * @return Config[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function updateValues($configs, $inputs)
    {
        $configs->each(function (Config $config) use ($inputs) {
            if (key_exists($config->slug, $inputs)) {
                $config->update(['value' => $inputs[$config->slug]]);
            }
        });

        return $configs->pluck('value', 'slug');
    }
}
