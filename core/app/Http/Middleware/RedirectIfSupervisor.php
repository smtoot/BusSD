<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfSupervisor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'supervisor')
    {
        if (Auth::guard($guard)->check()) {
            return redirect()->route('supervisor.dashboard');
        }
        return $next($request);
    }
}
