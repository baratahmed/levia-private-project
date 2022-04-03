<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class DeliveryRep
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
        if (Auth::guard('api')->check() && \auth('api')->user()->isDeliveryRep) {
            // request passed
        } else {
            return response([
                'message' => 'Unauthenticated',
                'data' => 'You need to be logged in as a delivery representative.'
            ], 401);
        }
        return $next($request);
    }
}
