<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Laravel\Spark\Spark;

class UserOwnsMerchant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Spark::usesTeams()) {
            if (! $request->user()->hasTeams() || ! $request->user() || ! $request->merchant || ($request->user()->roleOn($request->merchant) != 'owner' && ! $request->user()->ownsTeam($request->merchant))) {
                return abort(403, 'You are not authorized to perform this action.');
            }
        }

        return $next($request);
    }
}
