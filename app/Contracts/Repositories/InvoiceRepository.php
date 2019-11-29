<?php

namespace App\Contracts\Repositories;

interface InvoiceRepository
{


    /**
     * @param $merchantObj
     * @return mixed
     */
    public function getId($merchantObj);


    /**
     * @param $user
     * @param $points
     * @return mixed
     */
    public function createStripe($invoiceObj);

    /**
     * @param $invoiceObj
     * @return mixed
     */
    public function createShopify($invoiceObj);

    /**
     * @param $user
     * @param $points
     * @return mixed
     */
    public function update($user, $points);


    /**
     * @param $user
     * @return mixed
     */
    public function delete($user);

    /**
     * @return mixed
     */
    public function generate();
}
