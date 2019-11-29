<?php

namespace App\Http\Middleware\Permissions;

use Closure;
use App\Repositories\MerchantRepository;

class PointsSpendingPermissions
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
            ->checkPermitionByTypeCode(\Config::get('permissions.typecode.VariableDiscountCoupons')))
        {
            session(['show_upsell' => true]);
            return redirect(route('points.spending'));
        }

        return $next($request);
    }
}
