<?php

namespace App\Http\Middleware\Permissions;

use Closure;
use App\Repositories\MerchantRepository;

class ReferralPermissions
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
        $merchantRepo = new MerchantRepository();
        if(!$merchantRepo
            ->getCurrent()
            ->checkPermitionByTypeCode(\Config::get('permissions.typecode.ReferralProgram')))
        {
            return redirect(route('referrals.upgrade'));
        }

        return $next($request);
    }
}