<?php

namespace App\Transformers;

use App\Models\MerchantDetail;
use League\Fractal\TransformerAbstract;

class OwnMerchantDetailsTransformer extends TransformerAbstract
{
    public function transform(MerchantDetail $details)
    {
        return [
            'id'          => $details->id,
            'merchant_id' => $details->merchant_id,
            'shop_domain' => $details->ecommerce_shop_domain,
            'api_key'     => $details->api_key,
            'api_secret'  => $details->api_secret,
            'created_at'  => $details->created_at,
            'updated_at'  => $details->updated_at,
        ];
    }
}