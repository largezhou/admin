<?php

namespace App\Http\Middleware;

use App\Traits\UrlWhitelist;
use App\Utils\Admin;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    use UrlWhitelist;
    protected $urlWhitelist = [
        '/configs/system_basic/values',
    ];

    public function handle($request, Closure $next, ...$guards)
    {
        if ($this->shouldPassThrough($request)) {
            return $next($request);
        } else {
            return parent::handle(...func_get_args());
        }
    }

    protected function urlWhitelist(): array
    {
        return array_map(function ($url) {
            return Admin::url($url);
        }, $this->urlWhitelist);
    }
}
