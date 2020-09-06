<?php

namespace App\Admin\Models;

use App\Admin\Casts\Json;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class Config extends Model
{
    const TYPE_INPUT = 'input';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_FILE = 'file';
    const TYPE_SINGLE_SELECT = 'single_select';
    const TYPE_MULTIPLE_SELECT = 'multiple_select';
    const TYPE_OTHER = 'other';
    const CACHE_KEY = 'admin_config';
    const CONFIG_KEY = 'admin';

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
        'options' => Json::class,
        'value' => Json::class,
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
     * @param Config[]|\Illuminate\Support\Collection $configs
     * @param array $inputs slug => value 键值对
     *
     * @return Config[]|\Illuminate\Support\Collection
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

    public static function getConfigValue(string $slug, $default = null)
    {
        return static::query()->where('slug', $slug)->value('value') ?? $default;
    }

    protected static function getConfigGroupsFromDB(): array
    {
        return ConfigCategory::query()
            ->select(['id', 'slug'])
            ->with('configs:category_id,slug,value')
            ->get()
            ->map(function (ConfigCategory $category) {
                return [
                    'slug' => $category->slug,
                    'configs' => $category->configs->pluck('value', 'slug')->toArray(),
                ];
            })
            ->pluck('configs', 'slug')
            ->toArray();
    }

    /**
     * 如果直接用 config 函数获取配置，则已经是服务提供者里被替换了的
     * 这里要获取原始的文件配置
     *
     * @return array
     */
    protected static function getConfigGroupsFromFile(): array
    {
        $configFilePath = app('path.config').'/'.static::CONFIG_KEY.'.php';

        $config = [];
        if (file_exists($configFilePath) && !is_array($config = include $configFilePath)) {
            $config = [];
        }

        return $config;
    }

    public static function getDottedConfigFromCache(): array
    {
        return Cache::rememberForever(static::CACHE_KEY, function () {
            return array_merge(
                static::dotConfigs(static::getConfigGroupsFromFile()),
                static::dotConfigs(static::getConfigGroupsFromDB())
            );
        });
    }

    protected static function dotConfigs(array $configs)
    {
        return Arr::dot($configs, static::CONFIG_KEY.'.');
    }

    public static function loadToConfig()
    {
        config(static::getDottedConfigFromCache());
    }

    public static function clearConfigCache()
    {
        Cache::forget(static::CACHE_KEY);
    }
}
