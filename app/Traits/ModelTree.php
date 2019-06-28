<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * 嵌套结构模型辅助 trait
 *
 * Trait ModelTree
 * @package App\Traits
 */
trait ModelTree
{
    /**
     *
     *
     * @var int
     */
    protected $except = 0;

    protected function parentColumn()
    {
        return 'parent_id';
    }

    protected function orderColumn()
    {
        return 'order';
    }

    protected function idColumn()
    {
        return 'id';
    }

    /**
     * 排除指定的 id，排除后，该 id 和其子孙记录，都会排除
     *
     * @param int $id
     *
     * @return $this
     */
    public function treeExcept(int $id)
    {
        $this->except = $id;
        return $this;
    }

    /**
     * 构建嵌套数组
     *
     * @return array
     */
    public function toTree(): array
    {
        return $this->buildNestedArray();
    }

    /**
     * 构建嵌套数组
     *
     * @param array $nodes
     * @param int $parentId
     *
     * @return array
     */
    protected function buildNestedArray(array $nodes = [], $parentId = 0): array
    {
        $branch = [];
        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        static $parentIds;
        $parentIds = $parentIds ?: array_flip(array_column($nodes, $this->parentColumn()));

        foreach ($nodes as $node) {
            if ($node[$this->parentColumn()] == $parentId) {
                $children = $this->buildNestedArray($nodes, $node[$this->idColumn()]);

                if ($children) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }

        return $branch;
    }

    /**
     * 按排序查出所有记录
     *
     * @return array
     */
    protected function allNodes(): array
    {
        return static::query()
            ->when($this->except, function (Builder $query) {
                $query->where($this->idColumn(), '<>', $this->except)
                    ->where($this->parentColumn(), '<>', $this->except);
            })
            ->orderBy($this->orderColumn())
            ->get()
            ->toArray();
    }

    public function children()
    {
        return $this->hasMany(static::class, $this->parentColumn(), $this->idColumn());
    }

    public function parent()
    {
        return $this->belongsTo(static::class, $this->parentColumn(), $this->idColumn());
    }

    public function delete()
    {
        $this->children->each->delete();
        return parent::delete();
    }
}
