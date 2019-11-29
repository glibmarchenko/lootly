<?php

namespace App\Transformers;

use App\Models\Customer;
use App\Models\MerchantDetail;
use League\Fractal\TransformerAbstract;

class MerchantDetailsTransformer extends TransformerAbstract
{

    public function transform(MerchantDetail $details)
    {
        return [
            'id' => $details->id,
            'merchant_id' => $details->merchant_id,
            'shop_domain' => $details->ecommerce_shop_domain,
            'created_at' => $details->created_at,
            'updated_at' => $details->updated_at,
        ];
    }

}