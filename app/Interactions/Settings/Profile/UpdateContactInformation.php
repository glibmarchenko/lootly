<?php

namespace App\Interactions\Settings\Profile;

use Illuminate\Support\Facades\Validator;
use Laravel\Spark\Events\Profile\ContactInformationUpdated;

class UpdateContactInformation extends \Laravel\Spark\Interactions\Settings\Profile\UpdateContactInformation
{
    /**
     * {@inheritdoc}
     */
    public function validator($user, array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
//            'billing_email' => 'emailunique:users,billing_email,'.$user->billing_email,
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function handle($user, array $data)
    {
        $user->forceFill([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'billing_email' => $data['billing_email'],
        ])->save();

        event(new ContactInformationUpdated($user));

        return $user;
    }
}
