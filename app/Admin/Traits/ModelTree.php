<?php

namespace App\Admin\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * 嵌套结构模型辅助 trait
 *
 * Trait ModelTree
 * @package App\Admin\Traits
 */
trait ModelTree
{
    /**
     * 要排除的节点 id，子元素都会被排除
     *
     * @var int
     */
    protected $except = 0;
    protected static $branchOrder = [];

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
            if ($this->ignoreTreeNode($node)) {
                continue;
            }

            if ($node[$this->parentColumn()] == $parentId) {
                $children = $this->buildNestedArray($nodes, $node[$this->idColumn()]);
                // 没有子菜单也显示一个空的数组，避免前端没有 children 时，不能响应式
                $node['children'] = $children;
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
        return $this->allNodesQuery()->get()->toArray();
    }

    /**
     * @return Builder|mixed
     */
    protected function allNodesQuery(): Builder
    {
        return static::query()
            ->when($this->except, function (Builder $query) {
                $query->where($this->idColumn(), '<>', $this->except)
                    ->where($this->parentColumn(), '<>', $this->except);
            })
            ->orderBy($this->orderColumn());
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

    /**
     * 是否跳过节点的处理
     *
     * @param array $node 当前节点
     *
     * @return bool
     */
    protected function ignoreTreeNode(array $node): bool
    {
        return false;
    }

    protected function setBranchOrder(array $order)
    {
        static::$branchOrder = array_flip(Arr::flatten($order));

        static::$branchOrder = array_map(function ($item) {
            return ++$item;
        }, static::$branchOrder);
    }

    public function saveOrder($tree = [], $parentId = 0)
    {
        if (empty(static::$branchOrder)) {
            $this->setBranchOrder($tree);
        }

        foreach ($tree as $branch) {
            /** @var ModelTree $node */
            $node = static::find($branch[$this->idColumn()]);

            $node->{$node->parentColumn()} = $parentId;
            $node->{$node->orderColumn()} = static::$branchOrder[$branch[$this->idColumn()]];
            $node->save();

            if (isset($branch['children'])) {
                static::saveOrder($branch['children'], $branch[$this->idColumn()]);
            }
        }
    }

    /**
     * toTree 的反向操作
     *
     * @param array $tree
     *
     * @return array
     */
    public function flatten(array $tree): array
    {
        $flatten = [];

        foreach ($tree as $item) {
            $children = Arr::pull($item, 'children', []);
            $flatten[] = $item;
            $flatten = array_merge($flatten, $this->flatten($children));
        }

        return $flatten;
    }
}
