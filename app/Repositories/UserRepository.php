<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Merchant;
use Laravel\Spark\Spark;
use Laravel\Spark\Repositories\UserRepository as SparkUserRepository;
use App\User;

class UserRepository extends SparkUserRepository
{
    /**
     * {@inheritdoc}
     */
    public function search($query, $excludeUser = null)
    {
        $search = Spark::user()->with('subscriptions');

        // If a user to exclude was passed to the repository, we will exclude their User
        // ID from the list. Typically we don't want to show the current user in the
        // search results and only want to display the other users from the query.
        if ($excludeUser) {
            $search->where('id', '<>', $excludeUser->id);
        }

        return $search->where(function ($search) use ($query) {
            $search->where('email', 'like', $query)
                ->orWhere('first_name', 'like', $query)
                ->orWhere('last_name', 'like', $query);
        })->get();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $user = Spark::user();

        $user->forceFill([
            'last_name' => $data['last_name'],
            'first_name' => $data['first_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'last_read_announcements_at' => Carbon::now(),
            'trial_ends_at' => Carbon::now()->addDays(Spark::trialDays()),
        ])->save();

        return $user;
    }

    public function hasUser($email)
    {
        $user = \App\User::query()->where('email', $email)->first();
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }

    public function getByEmail($email)
    {
        return User::query()->where('email', '=', $email)->first();
    }

    public function updateNotification(array $data)
    {
        Merchant::query()->where('id', '=', $data['id'])
            ->update([
                'notification' => isset($data['notification']) ? $data['notification'] : null,
            ]);
        return response()->json([
            'Messages' => 'Succes'
        ]);
    }

    public function update($user, array $data)
    {
        $updateData = [
            'first_name' => array_get($data, 'first_name'),
            'last_name' => array_get($data, 'last_name'),
            'email' => array_get($data, 'email'),
            'billing_email' => array_get($data, 'billing_email'),
        ];

        if(isset($data['password'])){
            $updateData['password'] = bcrypt(array_get($data, 'password'));
        }

        $user->forceFill($updateData)->save();
    }

    public function updateNotificationSettings($user, $notification_type_id, $status)
    {
        $user->notification_types()->syncWithoutDetaching([$notification_type_id => [
            'active' => boolval($status)
        ]]);
    }
}
