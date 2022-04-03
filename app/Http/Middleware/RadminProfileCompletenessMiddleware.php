<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\RadminProfileCompleteness;

class RadminProfileCompletenessMiddleware
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
        if (auth('radmin')->check()){
            $radmin = auth('radmin')->user();
            $profile = RadminProfileCompleteness::get($radmin);

            if ($profile->isNotComplete()){
                $profile->reCalculate($radmin);
                if ($profile->isNotComplete()){
                    return redirect($profile->getRedirectLink())->with('info', __('auth.moreinfo'));
                }
            }
        }


        return $next($request);
    }
}
