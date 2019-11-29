<?php

namespace App\Http\Middleware\Permissions;

use Closure;
use App\Repositories\MerchantRepository;

class RewardsPermissions
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
            ->checkPermitionByTypeCode(\Config::get('permissions.typecode.RewardsPage')))
        {
            return redirect(route('rewards.upgrade'));
        }

        return $next($request);
    }
}
