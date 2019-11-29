<?php

namespace App\Http\Middleware\Api\Widget;

use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\MerchantRepository;
use Closure;

class CustomerAuth
{
    protected $customers;

    public function __construct(CustomerRepository $customers)
    {
        $this->customers = $customers;
    }

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
        if (! $request->get('merchant_id') || ! trim($request->get('merchant_id'))) {
            return response()->json(['message' => 'Not authorized action'], 403);
        }

        $merchantModel = new MerchantRepository();
        $merchant = $merchantModel->find($request->get('merchant_id'), ['detail']);
        if (! $merchant) {
            return response()->json(['message' => 'Not authorized action'], 403);
        }

        if (! $request->get('customer')) {
            return response()->json(['message' => 'Not authorized action'], 403);
        }
        $customer_id = $request->get('customer')['id'] ?? null;
        $token = $request->get('customer')['signature'] ?? null;

        try {
            $customer = $this->customers->findWhereFirst([
                'ecommerce_id' => $customer_id,
                'merchant_id'  => $merchant->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Not authorized action'], 403);
        }

        if (! $customer || $customer->merchant_id !== $merchant->id) {
            return response()->json(['message' => 'Not authorized action'], 403);
        }

        $merchant_api_secret = null;
        if (isset($merchant->detail) && isset($merchant->detail->api_secret)) {
            $merchant_api_secret = trim($merchant->detail->api_secret);
        }
        if (! $merchant_api_secret) {
            return response()->json(['message' => 'Not authorized action'], 403);
        }
        $sign = md5($customer_id.$merchant_api_secret);
        if ($sign !== $token) {
            return response()->json(['message' => 'Not authorized action'], 403);
        }

        $request->merge(['customer_id' => $customer->id]);

        return $next($request);
    }
}
