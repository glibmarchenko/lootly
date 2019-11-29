<?php

namespace App\Repositories;


use App\Models\Currency;
use App\Models\Invoice;
use App\Contracts\Repositories\InvoiceRepository as InvoiceContractRepository;
use App\User;


class InvoicesRepository implements InvoiceContractRepository
{
    public function find($id)
    {

    }

    public function getId($merchantObj)
    {

    }

    /**
     * @param $merchantObj
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get($merchantObj)
    {
        return Invoice::query()->where('merchant_id', '=', $merchantObj)->get();
    }

    /**
     * @param SubscriptionInterface $subscriptionObj
     */
    public function createStripe($subscriptionObj)
    {
        $userObj = $this->getByCusId($subscriptionObj);
        $currency_id = $this->getCurrencyId($subscriptionObj);
        Invoice::query()->create([
            'user_id' => $userObj->id,
            'merchant_id' => $userObj->merchant->id,
            'total' => $subscriptionObj->getAmount(),
            'currency_id' => $currency_id,
            'billing_state' => $subscriptionObj->source->address_state,
            'billing_zip' => $subscriptionObj->source->address_zip,
            'billing_county' => $subscriptionObj->source->address_county,


        ]);
    }

    public function createShopify($subscriptionObj)
    {
        $userObj = \App\User::getAuthClient();
        $merchantObj = new MerchantRepository();
        $currency_id = $this->getCurrencyId($subscriptionObj);
        Invoice::query()->create([
            'user_id' => $userObj->id,
            'merchant_id' => $merchantObj->id,
            'total' => $subscriptionObj->price,
            'currency_id' => $currency_id,
        ]);
    }

    public function getByCusId($invoiceObj)
    {
        return User::query()->where('stripe_customer_id', '=', $invoiceObj->source->id)->with('merchant')->first;
    }

    public function getCurrencyId($subscriptionObj)
    {
        $currency = Currency::query()->where('name', '=', $subscriptionObj->currency)->first();
        if ($currency) {
            return $currency->id;
        } else {
            return Currency::query()->where('name', '=', 'USD')->first()->id;
        }
    }

    public function update($user, $points)
    {
        // TODO: Implement update() method.
    }

    public function delete($user)
    {
        // TODO: Implement delete() method.
    }

    public function generate()
    {
    }
}
