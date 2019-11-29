<?php

namespace App\Http\Middleware\Permissions;

use Closure;
use App\Repositories\MerchantRepository;

class PointsEarningPermissions
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
        if(preg_split("/\//",$request->path())[3] == "read-content") {
            if(!$merchantRepo
                ->getCurrent()
                ->checkPermitionByTypeCode(\Config::get('permissions.typecode.ReadContent')))
            {
                session(['show_read_content_upsell' => true]);
                return redirect(route('points.earning'));
            }
        } elseif(preg_split("/\//",$request->path())[3] == "trustspot-review"){
            if(!$merchantRepo
                ->getCurrent()
                ->checkPermitionByTypeCode(\Config::get('permissions.typecode.TrustSpotReview')))
            {
                session(['show_trust_spot_upsell' => true]);
                return redirect(route('points.earning'));
            }
        }
        return $next($request);
    }
}
