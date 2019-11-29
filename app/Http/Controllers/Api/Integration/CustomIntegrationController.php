<?php

namespace App\Http\Controllers\Api\Integration;

use App\Http\Controllers\Controller;
use App\Repositories\MerchantDetailRepository;
use App\Repositories\Contracts\CustomerRepository as Customers;
use Illuminate\Http\Request;

class CustomIntegrationController extends Controller
{

    protected $customers;
    protected $merchant;

    public function __construct(
        MerchantDetailRepository $merchantDetails,
        Customers $customers
    )
    {
        $this->customers = $customers;
        $merchantDetail = $merchantDetails->findBy('api_key', request('key'));

        if (!$merchantDetail) {
            abort(
                response()->json( ['error' => 'Merchant not found'], 404)
            );
        }

        $this->merchant = $merchantDetail->merchant;

        $data = [
            'key' => request('key')
        ];

        if( request('customer_ids') ) {
            $data = array_merge( $data, ['customer_ids' => request('customer_ids')]);
        }

        $local_hmac = base64_encode( hash_hmac('sha256', json_encode( $data ), $merchantDetail->api_secret, true ) );

        if( $local_hmac != urldecode( str_replace("+", "%2B", urlencode( request('hmac') ) ) ) ) {
            abort(
                response()->json( ['error' => 'Unauthorized'], 403)
            );
        }
    }


    public function getSinglePointBalance(Request $request)
    {
        $customer_id = request('customer_id');
        $customer = $this->customers->findWhereFirst([
            'ecommerce_id' => $customer_id,
            'merchant_id'  => $this->merchant->id,
        ]);
        $points_value = isset($customer->points) ? $customer->points->sum('point_value') : 0;
        return response()->json(['point_value' => $points_value]);
    }

    public function getMultiplePointBalance(Request $request)
    {
        $customer_points = [];
        $customer_ids = request('customer_ids');
        $customer_ids = explode(',', $customer_ids );
        foreach( $customer_ids as $customer_id ) {
            $customer = $this->customers->findWhereFirst([
                'ecommerce_id' => $customer_id,
                'merchant_id'  => $this->merchant->id,
            ]);
            $points_value = isset($customer->points) ? $customer->points->sum('point_value') : 0;
            $customer_points[] = [ $customer_id => $points_value ];

        }
        return response()->json( $customer_points );
    }
}
