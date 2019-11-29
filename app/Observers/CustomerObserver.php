<?php

namespace App\Observers;

use App\Models\Customer;
use Illuminate\Support\Facades\Log;
use App\Repositories\CacheRepository;

class CustomerObserver
{
    /**
     * Handle to the customer "created" event.
     *
     * @param  \App\Customer $customer
     *
     * @return void
     */
    public function created(Customer $customer)
    {
        //
    }

    /**
     * Handle the customer "updated" event.
     *
     * @param  \App\Customer $customer
     *
     * @return void
     */
    public function updated(Customer $customer)
    {
        CacheRepository::clearCache($customer);
    }

    /**
     * Handle the customer "deleted" event.
     *
     * @param  \App\Models\Customer $customer
     *
     * @return void
     */
    public function deleted(Customer $customer)
    {
        
    }
}