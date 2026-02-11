<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = null;
        $guards = ['co-owner', 'manager', 'supervisor', 'driver'];

        foreach ($guards as $guard) {
            if (auth()->guard($guard)->check()) {
                $user = auth()->guard($guard)->user();
                break;
            }
        }

        if ($user && $user->hasPermission($permission)) {
            return $next($request);
        }

        $notify[] = ['error', 'You do not have permission to access this module.'];
        return back()->withNotify($notify);
    }
}
