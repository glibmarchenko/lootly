<?php

namespace App\Transformers;

use App\Models\Customer;
use League\Fractal\TransformerAbstract;

class CustomerProfileTransformer extends TransformerAbstract
{
    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Customer $customer)
    {
        $referralLink = rtrim(env('REFERRAL_LINKS_DOMAIN', 'http://ref.lootly.io'), '/').'/'.$customer->referral_slug;

        try {
            if (isset($this->data) && isset($this->data['referral_settings'])) {
                if ($this->data['referral_settings']->referral_domain_status && trim($this->data['referral_settings']->referral_domain)) {
                    $referralLink = rtrim(trim($this->data['referral_settings']->referral_domain), '/').'/?loref='.$customer->referral_slug;
                }
            }
        }catch(\Exception $e){
            //
        }

        return [
            'name' => $customer->name,
            'email' => $customer->email,
            'currentPoints' => isset($customer->points) ? $customer->points->sum('point_value') : 'N/A',
            'totalEarnedPoints' => isset($customer->earned_points) ? $customer->earned_points->sum('point_value') : 'N/A',
            'totalSpent' => floatval(isset($customer->orders) ? $customer->orders->sum('total_price') : 0),
            'couponsUsed' => isset($customer->coupons) ? count($customer->coupons) : 'N/A',
            'vipTier' => $customer->tier ? $customer->tier->name : 'N/A',
            'lastSeen' => $customer->updated_at ? $customer->updated_at->diffForHumans() : 'N/A',
            'birthday' => $customer->getBirthday(),
            'referralLink' => $referralLink,
            'ecommerce_id' => $customer->ecommerce_id,
        ];
    }
}
