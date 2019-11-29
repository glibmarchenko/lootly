<?php

namespace App\Repositories;


use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\User;
use App\Contracts\Repositories\SubscriptionRepository as SubscriptionRepositoryContract;


class SubscriptionRepository implements SubscriptionRepositoryContract
{
    public function find($id)
    {
        return SubscriptionPlan::query()->where('id', '=', $id)->first();
    }

    public function get($merchantObj)
    {
        return Subscription::query()->where('merchant_id', '=', $merchantObj->id);
    }

    /**
     * @param $charge_plan
     * @param $merchant_id
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function createShopifySubscription($subscription, $merchantObj)
    {
        $userObj = User::getAuthClient();
        $subscription = Subscription::query()->create([
            'user_id' => $userObj->id,
            'merchant_id' => $merchantObj->id,
            'shopify_id' => $subscription->recurring_application_charge->id,
            'name' => $subscription->recurring_application_charge->name,
            'trial_ends_at' => $subscription->recurring_application_charge->trial_date,
        ]);
        return $subscription;
    }

    /**
     * @param $subscription
     * @param $merchantObj
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function createStripeSubscription($subscription, $merchantObj)
    {
        $userObj = User::getAuthClient();
        $subscription = Subscription::query()->create([
            'stripe_id' => $subscription->id,
            'user_id' => $userObj->id,
            'merchant_id' => $merchantObj->id,
            'stripe_product_id' => $merchantObj->items->product,
            'stripe_customer_id' => $merchantObj->customer,
            'name' => $subscription->name,
            'trial_ends_at' => $subscription->days_until_due,
            'ends_at' => $subscription->ended_at,
        ]);
        return $subscription;
    }

    /**
     *
     */
    public function update()
    {
        // TODO: Implement update() method.
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        Subscription::query()->where('shopify_id', '=', $id)->delete();
    }
}
