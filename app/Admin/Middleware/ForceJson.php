<?php

namespace App\Admin\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * 在请求头中加入 x-requested-with: XMLHttpRequest
 * 让所有响应自动转为 json
 *
 * Class ForceJson
 * @package App\Http\Middleware
 */
class ForceJson
{
    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
