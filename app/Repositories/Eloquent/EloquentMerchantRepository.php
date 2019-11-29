<?php

namespace App\Repositories\Eloquent;

use App\Merchant;
use App\Models\Currency;
use App\Repositories\Contracts\CurrencyRepository;
use App\Repositories\Contracts\MerchantDetailsRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\MerchantUserRepository;
use App\Repositories\RepositoryAbstract;
use App\User;
use Illuminate\Support\Facades\Log;
use Laravel\Spark\Repositories\TeamRepository;

class EloquentMerchantRepository extends RepositoryAbstract implements MerchantRepository
{
    protected $teamRepository;

    protected $merchantUsers;

    protected $merchantDetails;

    protected $currencies;

    /**
     * EloquentMerchantRepository constructor.
     *
     * @param \App\Repositories\Contracts\MerchantUserRepository    $merchantUsers
     * @param \App\Repositories\Contracts\MerchantDetailsRepository $merchantDetails
     * @param \App\Repositories\Contracts\CurrencyRepository        $currencies
     */
    public function __construct(
        MerchantUserRepository $merchantUsers,
        MerchantDetailsRepository $merchantDetails,
        CurrencyRepository $currencies
    ) {
        parent::__construct();
        $this->teamRepository = new TeamRepository();
        $this->merchantUsers = $merchantUsers;
        $this->merchantDetails = $merchantDetails;
        $this->currencies = $currencies;
    }

    public function __call($name, $arguments)
    {
        $this->teamRepository->$name($arguments);
    }

    public function entity()
    {
        return Merchant::class;
    }

    public function updateIntegrations(Merchant $merchant, $integrationId, array $data)
    {
        return $merchant->integrationsWithToken()->syncWithoutDetaching([$integrationId => $data]);
    }

    public function createMerchant(User $user, array $data)
    {
        $defaultCurrency = null;
        try {
            $defaultCurrency = $this->currencies->findWhereFirst([
                'name' => Currency::DEFAULT_CURRENCY_NAME,
            ]);
        } catch (\Exception $e) {
            // No default currency
        }

        $merchantData = [
            'owner_id'        => $user->id,
            'name'            => $data['name'],
            'slug'            => uniqid('store'),
            'website'         => isset($data['website']) ? trim($data['website']) : '',
            'billing_country' => $data['selectedCountry'],
            'currency'              => Currency::DEFAULT_CURRENCY_NAME,
            'currency_display_sign' => Currency::DEFAULT_CURRENCY_DISPLAY_SIGN,
        ];

        if ($defaultCurrency) {
            $merchantData['currency_id'] = $defaultCurrency->id;
            $merchantData['currency'] = $defaultCurrency->name;
        }

        $merchant = $this->create($merchantData);

        if (! $merchant->id) {
            return null;
        }

        // Create owner records in `merchant_users` table
        try {
            $this->merchantUsers->create([
                'user_id'     => $user->id,
                'merchant_id' => $merchant->id,
                'role'        => 'owner',
            ]);
        } catch (\Exception $e) {
            Log::error('Cannot create merchant owner record. Merchant #'.$merchant->id.', User #'.$user->id.'. '.$e->getMessage());
        }

        // Update merchant logo
        try {
            $merchant = $this->updateMerchantLogo($merchant, $data);
        } catch (\Exception $e) {
            //
        }

        // Create merchant details record
        try {
            $this->merchantDetails->updateOrCreate(['merchant_id' => $merchant->id], [
                'api_key'    => str_random(60),
                'api_secret' => str_random(60),
            ]);
        } catch (\Exception $e) {
            //
        }

        return $merchant;
    }

    private function updateMerchantLogo(Merchant $merchant, array $data)
    {
        $logo_url = null;
        $logo_name = null;

        $file = isset($data['logo']) && $data['logo'] ? $data['logo'] : null;
        if ($file) {
            $image = file_get_contents($file);

            $logo_name = $merchant->id.'_'.time().'_'.md5(uniqid().'_'.rand());
            $path = '/merchants/logo/'.$logo_name;
            try {
                \Storage::disk('s3')->put($path, $image, 'public');
                $logo_url = $path;
                if (isset($data['logo_name']) && trim($data['logo_name'])) {
                    $logo_name = trim($data['logo_name']);
                }
            } catch (\Exception $e) {

            }
        }

        $merchant->logo_url = $logo_url;
        $merchant->logo_name = $logo_name;
        $merchant->save();

        return $merchant;
    }

    public function orders($merchantId)
    {
        $orders = new EloquentOrderRepository();
        $orders->entity = $this->find($merchantId)->orders();

        return $orders;
    }

    public function referredOrders($id)
    {
        $orders = new EloquentOrderRepository();
        $orders->entity = $this->find($id)->referred_orders();

        return $orders;
    }
}