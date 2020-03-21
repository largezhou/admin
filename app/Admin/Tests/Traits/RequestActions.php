<?php

namespace App\Admin\Tests\Traits;

use Illuminate\Testing\TestResponse;
use Illuminate\Support\Str;

trait RequestActions
{
    /**
     * 特殊的 复数 => 单数
     *
     * @var array
     */
    protected $specialPluralSingularMap = [
        'system-media' => 'system-media',
    ];

    /**
     * 返回路由名前缀
     *
     * @return string
     */
    protected function routePrefix(): string
    {
        if (isset($this->routePrefix)) {
            return $this->routePrefix.'.';
        } else {
            return '';
        }
    }

    /**
     * 使用 route 函数时, 自动加上路由名前缀
     *
     * @param string $name
     * @param mixed  $parameters
     * @param bool   $absolute
     *
     * @return string
     */
    protected function route(string $name, $parameters = [], $absolute = true): string
    {
        return route($this->routePrefix().$name, $parameters, $absolute);
    }

    /**
     * 把短横线分割的复数的资源名, 转成下划线分割的单数
     *
     * @param string $name
     *
     * @return string
     */
    protected function varName(string $name): string
    {
        if ($t = $this->specialPluralSingularMap[$name] ?? null) {
            $name = $t;
        } else {
            $name = Str::singular($name);
        }

        return str_replace('-', '_', $name);
    }

    /**
     * 请求资源集合
     *
     * @param array  $params
     * @param string $name
     *
     * @return TestResponse
     */
    protected function getResources(array $params = [], string $name = null): TestResponse
    {
        $name = $name ?: $this->resourceName;
        return $this->get($this->route("{$name}.index", $params));
    }

    /**
     * 请求添加资源
     *
     * @param array  $data
     * @param string $name
     * @param array $routeParams
     *
     * @return TestResponse
     */
    protected function storeResource(array $data = [], string $name = null, array $routeParams = []): TestResponse
    {
        $name = $name ?: $this->resourceName;
        return $this->post($this->route("{$name}.store", $routeParams), $data);
    }

    /**
     * 请求软删除一个资源
     *
     * @param int    $id
     * @param string $name
     *
     * @return TestResponse
     */
    protected function destroyResource(int $id, string $name = null): TestResponse
    {
        $name = $name ?: $this->resourceName;
        return $this->delete($this->route("{$name}.destroy", [$this->varName($name) => $id]));
    }

    /**
     * 请求获取单个资源
     *
     * @param int    $id
     * @param string $name
     *
     * @return TestResponse
     */
    protected function getResource(int $id, string $name = null): TestResponse
    {
        $name = $name ?: $this->resourceName;
        return $this->get($this->route("{$name}.show", [$this->varName($name) => $id]));
    }

    /**
     * 请求更新一个资源
     *
     * @param int    $id
     * @param array  $data
     * @param string $name
     *
     * @return TestResponse
     */
    protected function updateResource(int $id, array $data = [], string $name = null): TestResponse
    {
        $name = $name ?: $this->resourceName;
        return $this->put($this->route("{$name}.update", [$this->varName($name) => $id]), $data);
    }

    /**
     * 请求编辑一个资源
     *
     * @param int    $id
     * @param string $name
     *
     * @return TestResponse
     */
    protected function editResource(int $id, string $name = null): TestResponse
    {
        $name = $name ?: $this->resourceName;
        return $this->get($this->route("{$name}.edit", [$this->varName($name) => $id]));
    }

    /**
     * 请求创建一个资源
     *
     * @param string $name
     *
     * @return TestResponse
     */
    protected function createResource(string $name = null): TestResponse
    {
        $name = $name ?: $this->resourceName;
        return $this->get($this->route("{$name}.create"));
    }
}
