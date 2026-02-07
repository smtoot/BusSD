<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfCoOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard= 'co-owner')
    {
        if (Auth::guard($guard)->check()) {
            return redirect()->route('co-owner.dashboard');
        }
        return $next($request);
    }
}
