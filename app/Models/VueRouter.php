<?php

namespace App\Models;

class VueRouter extends Model
{
    protected $casts = [
        'parent_id' => 'integer',
        'order' => 'integer',
        'cache' => 'bool',
        'menu' => 'bool',
    ];
    protected $fillable = ['parent_id', 'order', 'title', 'icon', 'path', 'cache', 'menu'];

    /**
     * 把路由构建成嵌套的数组结构
     *
     * @param array $nodes
     * @param int $parentId
     *
     * @return array
     */
    public static function buildNestedArray(array $nodes = [], $parentId = 0): array
    {
        $branch = [];
        if (empty($nodes)) {
            $nodes = static::orderBy('order')->get()->toArray();
        }

        foreach ($nodes as $node) {
            if ($node['parent_id'] == $parentId) {
                $children = static::buildNestedArray($nodes, $node['id']);

                if ($children) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }

        return $branch;
    }

    public function children()
    {
        return $this->hasMany(VueRouter::class, 'parent_id');
    }

    public function delete()
    {
        $this->children->each->delete();
        return parent::delete();
    }

    /**
     * parent_id 默认为 0 处理
     *
     * @param $value
     */
    public function setParentIdAttribute($value)
    {
        $this->attributes['parent_id'] = $value ?: 0;
    }

    public function setPathAttribute($path)
    {
        $this->attributes['path'] = $path ? ('/'.ltrim($path, '/')) : null;
    }
}
