<?php

namespace App\Transformers;

use App\Merchant;
use App\User;
use League\Fractal\TransformerAbstract;

class UserSettingsTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'current_merchant',
        'notifications'
    ];

    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'name' => $user->name,
            'email' => $user->email,
            'billing_email' => trim($user->billing_email) ? : $user->email,
            'current_merchant_id' => $user->current_team_id,
            'owns_current_merchant' => $user->roleOnCurrentTeam() === 'owner',
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }

    public function includeCurrentMerchant(User $user)
    {
        $merchant = $user->currentTeam();

        if(!$merchant){
            return $this->null();
        }

        return $this->item($merchant, new MerchantTransformer);
    }

    public function includeNotifications(User $user)
    {
        $notifications = $user->notification_types;

        if(!$notifications){
            return $this->null();
        }

        return $this->collection($notifications, new UserNotificationTypeTransformer);
    }

}