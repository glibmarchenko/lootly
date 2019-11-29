<?php

namespace App\Contracts\Repositories;

interface CustomerRepository
{

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);


    /**
     * @param $data
     * @return mixed
     */
    public function create($data, $merchantObj);


    /**
     * @param $customer_id
     * @param $merchantObj
     * @param $data
     * @return mixed
     */
    public function update($customer_id, $merchantObj, $data);


    /**
     * @param $user
     * @return mixed
     */
    public function delete($user);

    /**
     * @param $email
     * @return mixed
     */
    public function hasCustomer($email);

    /**
     * @param $merchantObj
     * @return mixed
     */
    public function getPoints($merchantObj);

    /**
     * @param $merchantObj
     * @param $customerId
     * @return mixed
     */
    public function getTags($merchantObj, $customerId);

    /**
     * @param $merchantObj
     * @param $customerId
     * @param $tags
     * @return mixed
     */

    public function storeTags($merchantObj, $customerId, $tags);
    /**
     * @param $merchantObj
     * @param $customerId
     * @param $tags
     * @return mixed
     */

    public function removeTags($merchantObj, $customerId, $tags);

    /**
     * @param $merchantObj
     * @param $customerId
     * @return mixed
     */
    public function getEarnedPoints($merchantObj, $customerId);

    /**
     * @param $merchantObj
     * @param $customerId
     * @return mixed
     */
    public function getSpentPoints($merchantObj, $customerId);

    /**
     * @param $merchantObj
     * @param $customerId
     * @return mixed
     */
    public function getOrders($merchantObj, $customerId);

    /**
     * @param $merchantObj
     * @param $customerId
     * @return mixed
     */
    public function getReferralOrders($merchantObj, $customerId);

    /**
     * @param $merchantObj
     * @param $customerId
     * @return mixed
     */
    public function getVipActivity($merchantObj, $customerId);

    /**
     * @param $merchantObj
     * @return mixed
     */
    public function get($merchantObj);

    /**
     * @param $customer
     * @param $rewardData
     * @return mixed
     */
    public function addReward($customer, $rewardData);
}
