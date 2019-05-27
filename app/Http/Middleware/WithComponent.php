<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class WithComponent
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var JsonResponse $res */
        $res = $next($request);
        $comp = null;
        if (isset($res->vueComponent)) {
            $comp = $res->vueComponent;
        } else {
            try {
                [$class, $method] = explode('@', app('router')->currentRouteAction());
                $comp = Str::kebab(Str::before(class_basename($class), 'Controller')).'/'.$method;
            } catch (\Exception $e) {
                $comp = null;
            }
        }

        $comp && $res->withHeaders([
            'Vue-Component' => $comp,
        ]);

        return $res;
    }
}
