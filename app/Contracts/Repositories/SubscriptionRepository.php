<?php

namespace App\Contracts\Repositories;



interface SubscriptionRepository
{

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param $merchantObj
     * @return mixed
     */
    public function get($merchantObj);

    /**
     * @param $user
     * @param $points
     * @return mixed
     */
    public function createShopifySubscription($subscription, $merchantObj);

    /**
     * @param $subscription
     * @return mixed
     */
    public function createStripeSubscription($subscriptionObj, $merchantObj);

    /**
     * @param $user
     * @param $points
     * @return mixed
     */
    public function update();


    /**
     * @param $user
     * @return mixed
     */
    public function delete($id);


}
