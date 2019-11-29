<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    /**
     * Handle to the order "created" event.
     *
     * @param  \App\Models\Order $order
     *
     * @return void
     */
    public function created(Order $order)
    {
        //
    }

    /**
     * Handle the order "updated" event.
     *
     * @param  \App\Models\Order $order
     *
     * @return void
     */
    public function updated(Order $order)
    {
        //
    }

    /**
     * Handle the order "deleted" event.
     *
     * @param  \App\Models\Order $order
     *
     * @return void
     */
    public function deleted(Order $order)
    {
        //
    }
}
