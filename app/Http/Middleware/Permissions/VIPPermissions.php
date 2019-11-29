<?php

namespace App\Http\Middleware\Permissions;

use Closure;
use App\Repositories\MerchantRepository;

class VIPPermissions
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
            ->checkPermitionByTypeCode(\Config::get('permissions.typecode.VIP_Program')))
        {
            return redirect(route('vip.upgrade'));
        }

        return $next($request);
    }
}
