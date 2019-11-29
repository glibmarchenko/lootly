<?php

namespace App\Http\Controllers;

use App\Repositories\MerchantRepository;
use App\Repositories\PointSettingsRepository;

class SparkController extends \Laravel\Spark\Http\Controllers\Controller
{
    public $merchantRepository;

    public $pointSettingsRepository;

    public function __construct(
        PointSettingsRepository $pointSettingsRepository,
        MerchantRepository $merchantRepository
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->pointSettingsRepository = $pointSettingsRepository;
    }

    public function getCommonData()
    {
        $merchantObj = $this->merchantRepository->getCurrent();
        $point_name = $this->pointSettingsRepository->get($merchantObj)->toArray();
        $company_logo = env('DefaultCompanyLogo');
        if (isset($merchantObj->email_notification_settings)) {
            $company_logo = $merchantObj->email_notification_settings->company_logo;
            $company_logo ? $company_logo : $company_logo = env('DefaultCompanyLogo');
        }
        if ($point_name == []) {
            $point_name[0] = [
                'name'        => 'Point',
                'plural_name' => 'Points',
            ];
        }
        $currency = $merchantObj->merchant_currency;
        $company = $merchantObj->name;

        $currencySign = '$';
        $displayCurrencyName = 0;
        if ($currency) {
            if ($merchantObj->currency_display_sign) {
                $currencySign = $currency->currency_sign;
            } else {
                $currencySign = $currency->name;
                $displayCurrencyName = 1;
            }
        }

        return response()->json([
            'currency'            => $currency,
            'currencySign'        => $currencySign,
            'displayCurrencyName' => $displayCurrencyName,
            'point'               => $point_name[0],
            'logo'                => $company_logo,
            'company'             => $company,
        ]);
    }
}
