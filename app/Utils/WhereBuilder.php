<?php

namespace App\Utils;

/**
 * Class WhereBuilder
 * @method $this equal(string|array $fields)
 * @method $this like(string|array $fields, $type = ['%?%', '?%', '%?'])
 * @package App\Utils
 */
class WhereBuilder
{
    /**
     * 请求中的数据
     *
     * @var array
     */
    protected $inputs = [];
    /**
     * 构建的查询
     *
     * @var array
     */
    protected $where = [];
    /**
     * 可用的构建方法
     *
     * @var array
     */
    protected $availableBuilder = [
        'equal', 'like',
    ];

    public function __construct($inputs = [])
    {
        $this->inputs = $inputs;
    }

    /**
     * 设置请求中的数据
     *
     * @param array $inputs
     * @param bool  $replace 是否覆盖原来的数据
     *
     * @return $this
     */
    public function setInputs($inputs, $replace = false)
    {
        if ($replace) {
            $this->inputs = $inputs;
        } else {
            $this->inputs = array_merge($this->inputs, $inputs);
        }

        return $this;
    }

    /**
     * 返回构建好的 where 数组
     *
     * @return array
     */
    public function toWhere(): array
    {
        return $this->where;
    }

    /**
     * 相等字段
     *
     * @param array $fields
     *
     * @return $this
     */
    protected function _equal($fields)
    {
        foreach ($fields as $i) {
            if ($q = $this->inputs[$i] ?? null) {
                $this->where[] = [$i, '=', $q];
            }
        }

        return $this;
    }

    /**
     * like 字段查询
     *
     * @param array  $fields
     * @param string $type ['%?%', '?%', '%?']
     *
     * @return $this
     */
    protected function _like($fields, $type = 'all')
    {
        if (!in_array($type, ['%?%', '?%', '%?'])) {
            throw new \InvalidArgumentException("不支持的类型: [ {$type} ]");
        }

        foreach ($fields as $i) {
            if ($q = $this->inputs[$i] ?? null) {
                $this->where[] = [$i, 'like', str_replace('?', $q, $type)];
            }
        }

        return $this;
    }

    public function __call($name, $arguments)
    {
        if (!in_array($name, $this->availableBuilder)) {
            throw new \InvalidArgumentException("构建方法: [ {$name} ] 不存在");
        }

        $fields = $arguments[0];
        if (!is_array($fields)) {
            $arguments[0] = [$fields];
        }

        $method = '_'.$name;
        return $this->$method(...$arguments);
    }
}
