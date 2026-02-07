<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RegistrationStep
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->guard('owner')->check()) {
            $user = auth()->guard('owner')->user();
            $route = 'owner.data';
        }

        if (!$user->profile_complete) {
            if ($request->is('api/*')) {
                $notify[] = 'Please complete your profile to go next';
                return response()->json([
                    'remark' => 'profile_incomplete',
                    'status' => 'error',
                    'message' => ['error' => $notify],
                ]);
            } else {
                return to_route($route);
                // return to_route('user.data');
            }
        }
        return $next($request);
    }
}
