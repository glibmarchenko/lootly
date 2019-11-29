<?php

namespace App\Transformers;

use App\Models\Customer;
use App\Models\MerchantDetail;
use League\Fractal\TransformerAbstract;

class MerchantAllDetailsTransformer extends TransformerAbstract
{
    public function transform(MerchantDetail $details)
    {
        return [
            'id'                    => $details->id,
            'merchant_id'           => $details->merchant_id,
            'shop_domain'           => $details->ecommerce_shop_domain,
            'currency'              => $details->currency,
            'display_currency_name' => $details->display_currency_name,
            'created_at'            => $details->created_at,
            'updated_at'            => $details->updated_at,
        ];
    }
}