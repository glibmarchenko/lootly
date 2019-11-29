<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaidPermission;
use App\Repositories\MerchantRepository;
use App\Repositories\CustomerRepository;
use App\Transformers\MerchantTransformer;
use Illuminate\Support\Facades\Cache;

class CustomersController extends Controller
{
    public function __construct()
    {
        $this->merchantRepo = new MerchantRepository();
        $this->customerRepo = new CustomerRepository();
    }

    public function index()
    {
        $feature = PaidPermission::getByTypeCode('EmailCustomization');
        $merchant = $this->merchantRepo->getCurrent();

        return view('customers.index', compact('feature', 'merchant'));
    }

    public function show(Request $request, $id)
    {
        $merchant = $this->merchantRepo->getCurrent();
        $profileData = $this->customerRepo->getProfileData($merchant, $id);
        $customer = $this->customerRepo->find($id);

        return view('customers.show', array_merge([
            'id' => $id,
            'tiers' => $merchant->tiers->pluck('name', 'id') ?? [],
            'customer' => $customer,
        ], $profileData));
    }

    /*
    private function GetData()
    {
        $merchantRepository = new MerchantRepository;
        $merchant = $merchantRepository->getCurrent();
        $company_logo = env('DefaultCompanyLogo');
        if (isset($merchant->email_notification_settings)) {
            $company_logo = $merchant->email_notification_settings->company_logo;
        }
        $data['company'] = $merchant->name;
        $data['company_logo'] = $merchant->logo_url ? $merchant->logo_url : $company_logo;

        $data['have_rest_permissions'] = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.CustomerSegmentation'));
        $data['restrictions_upsell'] = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.CustomerSegmentation'));

        $data['merchant_data'] = fractal()
            ->item($merchant)
            ->parseIncludes(['merchant_currency'])
            ->transformWith(new MerchantTransformer)
            ->toArray()['data'];

        try {
                $customer = $this->customers->withCriteria([
                    new EagerLoad([
                        'tier',
                        'orders',
                        'points',
                    ]),
                    new WithUsedCoupons(),
                    new WithEarnedPoints(),
                    new WithEarnedPointsInYear(),
                    //new WithTierHistory(),
                    new LatestFirst(),
                ])->find($customer_id);
            } catch (\Exception $e) {
                return response()->json([], 404);
            }
        return $data;
    }
    */
}