<?php

namespace App\Models;

use App\Utils\Admin;
use Illuminate\Database\Eloquent\Builder;

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

    public function delete()
    {
        $this->children->each->delete();
        return parent::delete();
    }

    /**
     * 构建树状结构数组
     *
     * @param int|null $except 要排除的某个 id
     * @param array $nodes
     * @param int $parentId
     *
     * @return array
     */
    public static function buildNestedArray(int $except = null, array $nodes = [], $parentId = 0): array
    {
        $branch = [];
        if (empty($nodes)) {
            $nodes = static::query()
                ->when($except, function (Builder $query) use ($except) {
                    $query->where('id', '<>', $except)->where('parent_id', '<>', $except);
                })
                ->orderBy('order')
                ->get()
                ->toArray();
        }

        foreach ($nodes as $node) {
            if ($node['parent_id'] == $parentId) {
                $children = static::buildNestedArray($except, $nodes, $node['id']);

                if ($children) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }

        return $branch;
    }
}
