<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait UrlWhitelist
{
    /**
     * 获取 url 白名单
     *
     * @return array
     */
    protected function urlWhitelist(): array
    {
        if (property_exists($this, 'urlWhitelist')) {
            return $this->urlWhitelist;
        } else {
            return [];
        }
    }

    /**
     * 检测请求的 url 是不是在白名单中
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        foreach ($this->urlWhitelist() as $url) {
            $methods = [];
            if (Str::contains($url, ':')) {
                [$methods, $url] = explode(':', $url);
                $methods = explode(',', $methods);
            }

            if ($url !== '/') {
                $url = trim($url, '/');
            }

            if (
                $this->isAnyMethod($request, $methods)
                && ($request->fullUrlIs($url) || $request->is($url))
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * 判断当前请求的方法是否在 $methods 中，或者 $methods 为空
     *
     * @param Request $request
     * @param array $methods
     *
     * @return bool
     */
    protected function isAnyMethod(Request $request, array $methods = []): bool
    {
        if (empty($methods)) {
            return true;
        }

        foreach ($methods as $method) {
            if ($request->isMethod($method)) {
                return true;
            }
        }

        return false;
    }
}
