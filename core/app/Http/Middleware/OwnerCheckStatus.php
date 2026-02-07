<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class OwnerCheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'owner')
    {
        if (Auth::guard($guard)->check()) {
            $owner = authUser();
            if ($owner->status  && $owner->ev  && $owner->sv) {
                return $next($request);
            } else {
                return to_route('owner.authorization');
            }
        }
        abort(403);
    }
}
