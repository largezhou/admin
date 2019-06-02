<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminMenu extends Model
{
    protected $casts = [
        'parent_id' => 'integer',
        'order' => 'integer',
        'cache' => 'bool',
        'is_menu' => 'bool',
    ];
    protected $fillable = ['parent_id', 'order', 'title', 'icon', 'uri', 'cache', 'is_menu'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (AdminMenu $model) {
            if ($model->isDirty('uri')) {
                $model->uri = '/'.ltrim($model->uri, '/');
            }
        });
    }

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

    public function children()
    {
        return $this->hasMany(AdminMenu::class, 'parent_id');
    }

    public function delete()
    {
        DB::beginTransaction();

        $res = parent::delete();
        $this->children->each->delete();

        DB::commit();

        return $res;
    }
}
