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

    /**
     * 把菜单构建成嵌套的数组结构
     *
     * @param array $nodes
     * @param int   $parentId
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
}
