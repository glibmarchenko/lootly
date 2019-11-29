<?php

namespace App\Transformers;

use App\Models\Customer;
use League\Fractal\TransformerAbstract;

class CustomerTransformer extends TransformerAbstract
{

    protected $data;

    protected $availableIncludes = [
        'tier'
    ];

    public function __construct($data = [])
    {
        $this->data = $data;
    }

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
        $total_spend = 0;
        if(count($customer->orders) > 0) {
            foreach ($customer->orders as $order) {
                $total_spend += ($order->total_price - $order->refunded_total);
            }
        }

        return [
            'id' => $customer->id,
            'merchant_id' => $customer->merchant_id,
            'name' => trim($customer->name) ? : '#'.$customer->id,
            'email' => $customer->email,
            'country' => $customer->country,
            'zipcode' => $customer->zipcode,
            'birthday' => isset($customer->birthday) && $customer->birthday && $customer->birthday != '0000-00-00' ? date('m/d/Y', strtotime($customer->birthday)) : '',
            'referral_slug' => $customer->referral_slug,
            'shares_count' => $customer->shares_count,
            'clicks_count' => $customer->clicks_count,
            'referral_link' => $referralLink,
            'tier_id' => $customer->tier_id,
            'vip_tier' => $customer->vip_tier,
            //'tier' => isset($customer->tier) ? $customer->tier : [],
            'tier_history' => isset($customer->tier_history) ? $customer->tier_history : [],
            'points' => isset($customer->points) ? $customer->points->sum('point_value') : 0,
            // 'points_earned' => $customer->earned_points ?? 0, REVERT CHANGES FOR TEMP HOT FIX
            'points_earned' => isset($customer->earned_points) && is_array($customer->earned_points) ? $customer->earned_points->sum('point_value') : (isset($customer->earned_points) ? $customer->earned_points : 0),
            'points_earned_in_year' => isset($customer->earned_points_in_year) ? $customer->earned_points_in_year->sum('point_value') : 0,
            // 'completed_actions' => isset($customer->earned_points) ? array_filter($customer->earned_points->unique('merchant_action_id')->pluck('merchant_action_id')->toArray()) : [],
            'total_spend_nf' => $customer->total_spend ? number_format($customer->total_spend, 2, '.', ',') : $customer->total_spend,
            'total_spend' => $total_spend,
            'purchases' => $customer->purchases,
            'coupons_used' => isset($customer->coupons) ? count($customer->coupons) : 0,
            'ecommerce_id' => $customer->ecommerce_id,
            'rewards_spending_limits' => $this->data['rewards_spending_limits'] ?? [],
            'reward_coupons' => $this->data['reward_coupons'] ?? [],
            'created_at' => $customer->created_at,
            'created_at_ts' => $customer->created_at ? $customer->created_at->timestamp : null,
            'updated_at' => $customer->updated_at,
            'last_seen' => $customer->updated_at ? $customer->updated_at->diffForHumans() : null
        ];
    }


    public function includeTier(Customer $customer)
    {
        $tier = $customer->tier;

        if(!$tier){
            return null;
        }

        return $this->item($tier, new TierTransformer);
        //return $this->collection($customer->tier, new TierTransformer);
    }


}