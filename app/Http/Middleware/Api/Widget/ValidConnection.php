<?php

namespace App\Http\Middleware\Api\Widget;

use App\Repositories\MerchantDetailRepository;
use Closure;

class ValidConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $request->get('shop')) {
            return response()->json(['message' => 'Not authorized connection'], 403);
        }

        if (! isset($request->get('shop')['domain']) || ! trim($request->get('shop')['domain']) || ! isset($request->get('shop')['signature']) || ! trim($request->get('shop')['signature'])) {
            return response()->json(['message' => 'Not authorized connection'], 403);
        }

        $request_shop = trim($request->get('shop')['domain']);
        $request_signature = trim($request->get('shop')['signature']);

        $merchantDetailModel = new MerchantDetailRepository();

        $merchant_details = $merchantDetailModel->getByShopDomain($request_shop);

        if (! count($merchant_details)) {
            return response()->json(['message' => 'Not authorized connection'], 403);
        }

        foreach ($merchant_details as $merchant_detail) {
            $signature = md5($merchant_detail->ecommerce_shop_domain.$merchant_detail->api_secret);
            if ($signature === $request_signature) {
                $request->merge(['merchant_id' => $merchant_detail->merchant_id]);

                return $next($request);
                break;
            }
        }

        return response()->json(['message' => 'Not authorized connection'], 403);
    }
}
