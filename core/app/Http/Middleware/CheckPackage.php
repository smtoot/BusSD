<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Carbon\Carbon;

class CheckPackage
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
        $general = gs();

        if ($guard == 'owner') {
            $owner = authUser();
        } elseif ($guard == 'co-owner') {
            $owner = authUser('co-owner')->owner;
        } elseif ($guard == 'manager') {
            $owner = authUser('manager')->owner;
        }

        $notify[] = ['error', 'You don\'t have any active package to access this menu.'];
        $packageCount = $owner->activePackages()->count();

        if ($packageCount == 0 && $general->package_id == null) {
            return to_route("$guard.dashboard")->withNotify($notify);
        }

        if ($packageCount == 0) {
            $package    = $general->package;
            $start_from = $owner->created_at;

            $ends_at    = getPackageExpireDate($package->time_limit, $package->unit, $start_from);

            if ($ends_at < Carbon::now()) {
                return to_route("$guard.dashboard")->withNotify();
            }
        }

        return $next($request);
    }
}
