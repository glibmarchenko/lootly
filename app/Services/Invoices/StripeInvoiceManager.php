<?php


class StripeInvoiceManager extends InvoiceManagerAbstract
{


    protected $stripe;

    public function __construct($strype)
    {
        $this->stripe = $strype;
    }


    public function createInvoice($subscriptionObj)
    {

        // TODO: Implement createInvoice() method.
    }
}