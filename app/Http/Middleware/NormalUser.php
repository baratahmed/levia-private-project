<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class NormalUser
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
        if (Auth::guard('api')->check() && \auth('api')->user()->isNormalUser) {

        } else {
            // dd(\auth('api')->user());
            return response([
                'message' => 'Unauthenticated',
                'data' => 'You need to be logged in as a normal user.'
            ], 401);
        }
        return $next($request);
    }
}
