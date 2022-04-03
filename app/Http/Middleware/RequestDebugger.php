<?php

namespace App\Http\Middleware;

use Closure;

class RequestDebugger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        echo "{\"accept\":\"".$request->header('accept')."\",";
        echo "\"authorization\":\"".$request->header('authorization')."\"}";
        return $next($request);
    }
}
