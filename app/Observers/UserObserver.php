<?php

namespace App\Observers;

use App\User;
use Illuminate\Support\Facades\Log;
use App\Repositories\CacheRepository;

class UserObserver
{
    /**
     * Handle to the user "created" event.
     *
     * @param  \App\User $user
     *
     * @return void
     */
    public function created(User $user)
    {
        //
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\User $user
     *
     * @return void
     */
    public function updated(User $user)
    {
        CacheRepository::clearCache($user);
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\Models\User $user
     *
     * @return void
     */
    public function deleted(User $user)
    {
        
    }
}