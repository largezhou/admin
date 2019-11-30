<?php

namespace App\Admin\Traits;

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
            if ($url !== '/') {
                $url = trim($url, '/');
            }

            if ($request->fullUrlIs($url) || $request->is($url)) {
                return true;
            }
        }

        return false;
    }
}
