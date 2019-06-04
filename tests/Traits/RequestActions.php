<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Str;

trait RequestActions
{
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
     * @param array|string $name
     * @param mixed        $parameters
     * @param bool         $absolute
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
        return str_replace('-', '_', Str::singular($name));
    }

    /**
     * 请求资源集合
     *
     * @param string $resourceName
     * @param array  $params
     *
     * @return TestResponse
     */
    protected function getResources(string $resourceName, array $params = []): TestResponse
    {
        return $this->get($this->route("{$resourceName}.index"), $params);
    }

    /**
     * 请求添加资源
     *
     * @param string $name
     * @param array  $data
     *
     * @return TestResponse
     */
    protected function storeResource(string $name, array $data = []): TestResponse
    {
        return $this->post($this->route("{$name}.store"), $data);
    }

    /**
     * 请求软删除一个资源
     *
     * @param string $name
     * @param int    $id
     *
     * @return TestResponse
     */
    protected function destroyResource(string $name, int $id): TestResponse
    {
        return $this->delete($this->route("{$name}.destroy", [$this->varName($name) => $id]));
    }

    /**
     * 请求获取单个资源
     *
     * @param string $name
     * @param int    $id
     *
     * @return TestResponse
     */
    protected function getResource(string $name, int $id): TestResponse
    {
        return $this->get($this->route("{$name}.show", [$this->varName($name) => $id]));
    }

    /**
     * 请求更新一个资源
     *
     * @param string $name
     * @param int    $id
     * @param array  $data
     *
     * @return TestResponse
     */
    protected function updateResource(string $name, int $id, array $data = []): TestResponse
    {
        return $this->put($this->route("{$name}.update", [$this->varName($name) => $id]), $data);
    }

    /**
     * 请求编辑一个资源
     *
     * @param string $name
     * @param int    $id
     *
     * @return TestResponse
     */
    protected function editResource(string $name, int $id): TestResponse
    {
        return $this->get($this->route("{$name}.edit", [$this->varName($name) => $id]));
    }

    /**
     * 请求创建一个资源
     *
     * @param string $name
     * @param int    $id
     *
     * @return TestResponse
     */
    protected function createResource(string $name, int $id): TestResponse
    {
        return $this->get($this->route("{$name}.create", [$this->varName($name) => $id]));
    }
}
