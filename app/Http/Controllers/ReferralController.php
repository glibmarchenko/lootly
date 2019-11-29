<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Http\Request;
use App\Repositories\MerchantRepository;
use App\Models\MerchantReward;
use App\Models\PaidPermission;
use App\Repositories\MerchantRewardRepository;
use App\Repositories\ReportsRepository;
use Illuminate\Support\Facades\Log;

class ReferralController extends Controller
{
    protected $customers;

    public function __construct(CustomerRepository $customers)
    {
        $this->customers = $customers;
        $this->merchantRepository = new MerchantRepository();
        $this->merchantRewardRepository = new MerchantRewardRepository();
        $this->recieverRewardTypeId = MerchantReward::REWARD_TYPE_REFERRAL_RECEIVER;
        $this->reportsRepository = new ReportsRepository;
    }

    public function index(Request $request, $referral_slug)
    {
        $referral_slug = trim($referral_slug);

        if (! $referral_slug) {
            return abort(404);
        }

        // Get customer with this referral slug. Eager load merchant with merchant_details
        try {
            $customer = $this->customers->withCriteria([
                new EagerLoad(['merchant.detail']),
            ])->findWhereFirst([
                'referral_slug' => $referral_slug,
            ]);
        } catch (\Exception $e) {
            return abort(404);
        }

        if (! isset($customer->merchant->detail) && ! isset($customer->merchant->detail->ecommerce_shop_domain)) {
            return abort(404);
        }

        $shop_domain = trim($customer->merchant->detail->ecommerce_shop_domain);

        if (! $shop_domain) {
            return abort(404);
        }

        $shop_domain = rtrim(preg_replace('/https?:\/\//i', '', $shop_domain), '/');
        $shop_domain .= (strpos($shop_domain, '?') === false) ? '?' : '&';
        $shop_domain .= 'loref='.$referral_slug;

        if( !$_SERVER['HTTP_USER_AGENT'] || stripos( $_SERVER['HTTP_USER_AGENT'], 'facebookexternalhit' ) === false ) {
            app('customer_service')->incrementReferralClickCounter($customer->id, $request->all());
        }

        return redirect('http://'.$shop_domain);
    }

    public function sharing(){
        $merchant = $this->merchantRepository->getCurrent();
        $company = $merchant->name;
        $referral_link = '{referral-link}';
        if(isset($merchant->referrals_settings)){
            $referral_link = $merchant->referrals_settings->referral_link;
        }
        $receiver = $this->merchantRewardRepository->getByTypeId($this->recieverRewardTypeId);
        if(!empty($receiver)){
            $receiver_reward = $receiver->reward_text;
        } else {
            $receiver_reward = '{reward-name}';
        }
        $company_website = $merchant->website;
        return view('referrals.sharing', compact('company', 'referral_link', 'receiver_reward', 'company_website'));
    }

    public function rewards(){
        if(!$this->merchantRepository // check if merchant has permission
            ->getCurrent()
            ->checkPermitionByTypeCode(\Config::get('permissions.typecode.ReferralProgram')))
        {
                return redirect(route('referrals.upgrade'));
        }
        return view('referrals.rewards.index');
    }

    public function activity() {
        return view('referrals.activity');
    }

    public function overview(){
        return view('referrals.overview');
    }

    public function getActivity(Request $request) {
        $merchant = $this->merchantRepository->getCurrent();
        $start = new \DateTime($request->get('start'));
        $orders = $this->reportsRepository->getOrders($merchant)
            ->where('created_at', '>', $start);
        $customers = [];
        foreach ($orders as $order) {
            if($merchant->detail) {
                $orderLink = 'https://'. $merchant->detail->ecommerce_shop_domain .'/admin/orders/'. $order->order_id;
            }
            else {
                $orderLink = "";
            }
            $customers[] = [
                'name' => $order->customer->name,
                'order_number' => $order->order_id,
                'order_total' => $order->total_price,
                'referred_name' => $order->referral->name,
                'date' => $order->created_at->format('Y-m-d H:i:s'),
                'customer_id' => $order->customer->id,
                'referred_id' => $order->referral->id,
                'orderLink' => $orderLink
            ];
        }
        return $customers;
    }
}
