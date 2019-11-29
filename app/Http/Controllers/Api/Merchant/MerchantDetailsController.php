<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Merchant;
use App\Repositories\Contracts\CurrencyRepository;
use App\Repositories\Contracts\MerchantDetailsRepository;
use App\Transformers\MerchantAllDetailsTransformer;
use App\Transformers\MerchantDetailsTransformer;
use Illuminate\Http\Request;

class MerchantDetailsController extends Controller
{
    protected $merchantDetails;

    protected $currencies;

    public function __construct(MerchantDetailsRepository $merchantDetails, CurrencyRepository $currencies)
    {
        $this->merchantDetails = $merchantDetails;
        $this->currencies = $currencies;
    }

    public function getDetails(Request $request, Merchant $merchant)
    {
        $details = $this->merchantDetails->findWhereFirst([
            'merchant_id' => $merchant->id,
        ]);

        return fractal($details)->transformWith(new MerchantDetailsTransformer())->toArray();
    }

    public function getAllDetails(Request $request, Merchant $merchant)
    {
        $details = $this->merchantDetails->findWhereFirst([
            'merchant_id' => $merchant->id,
        ]);

        $currencySign = '$';

        if ($merchant->currency_id) {
            try {
                $currency = $this->currencies->find($merchant->currency_id);
                if ($currency) {
                    if ($merchant->currency_display_sign) {
                        $currencySign = $currency->currency_sign;
                        $details->display_currency_name = 0;
                    } else {
                        $currencySign = $currency->name;
                        $details->display_currency_name = 1;
                    }
                }
            } catch (\Exception $e) {
                // No such currency in db
            }
        }

        $details->currency = $currencySign;

        return fractal($details)->transformWith(new MerchantAllDetailsTransformer())->toArray();
    }
}