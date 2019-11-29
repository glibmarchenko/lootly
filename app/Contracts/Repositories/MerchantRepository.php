<?php

namespace App\Contracts\Repositories;

use Laravel\Spark\Contracts\Repositories\TeamRepository;

interface MerchantRepository extends TeamRepository
{
    /**
     * @param $merchantObj
     * @return mixed
     */
//    public function getId($merchantObj);

    /**
     * @return mixed
     */
    public function get();

    /**
     * @param array $merchant
     * @return mixed
     */
    public function update(array $merchant);


    /**
     * @param $user
     * @return mixed
     */
    public function delete($user);

    public function getCurrent();

    public function getTags($merchantObj);

    /**
     * @param $userId - Owner ID
     * @param $data - Merchant request data
     *
     * @return mixed
     */
    public function createMerchant($userId, $data);

    /**
     * @param $ownerId - Owner ID
     *
     * @return mixed
     */
    public function getMerchantsByOwner($ownerId);

    /**
     * @param $ownerId - Owner ID
     * @param $merchantId - Merchant ID
     *
     * @return mixed
     */
    public function findOwnedMerchantById($ownerId, $merchantId);
}
