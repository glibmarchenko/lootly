<?php

namespace App\Helpers;

use App\Models\Currency;
use App\Repositories\Contracts\CurrencyRepository;
use App\Repositories\Contracts\InvitationRepository;
use App\Repositories\Contracts\MerchantDetailsRepository;
use App\Repositories\Contracts\UserRepository;
use Carbon\Carbon;
use Laravel\Spark\Contracts\Interactions\Settings\Teams\AddTeamMember;
use Laravel\Spark\Contracts\Interactions\Settings\Teams\CreateTeam;
use Laravel\Spark\Events\Auth\UserRegistered;

class UserService
{
    protected $users;

    protected $invitations;

    protected $merchantDetails;

    protected $currencies;

    /**
     * UserService constructor.
     *
     * @param \App\Repositories\Contracts\UserRepository            $users
     * @param \App\Repositories\Contracts\InvitationRepository      $invitations
     * @param \App\Repositories\Contracts\MerchantDetailsRepository $merchantDetails
     * @param \App\Repositories\Contracts\CurrencyRepository        $currencies
     */
    public function __construct(
        UserRepository $users,
        InvitationRepository $invitations,
        MerchantDetailsRepository $merchantDetails,
        CurrencyRepository $currencies
    ) {
        $this->users = $users;
        $this->invitations = $invitations;
        $this->merchantDetails = $merchantDetails;
        $this->currencies = $currencies;
    }

    public function createNewUser(array $data = [])
    {
        $userData = [
            'first_name'                 => $data['first_name'] ?? '',
            'last_name'                  => $data['last_name'] ?? '',
            'email'                      => $data['email'],
            'password'                   => bcrypt($data['password']),
            'last_read_announcements_at' => Carbon::now(),
            'trial_ends_at'              => Carbon::now()->addDays(\Spark::trialDays()),
        ];

        $user = $this->users->create($userData);

        event(new UserRegistered($user));

        return $user;
    }

    public function configureMerchant($user, array $data = [])
    {
        $invitation = null;

        if (isset($data['invitation'])) {
            try {
                $invitation = $this->invitations->findWhereFirst(['token' => $data['invitation']]);
            } catch (\Exception $exception) {
                //
            }
        }

        $merchant = null;

        if ($invitation) {
            \Spark::interact(AddTeamMember::class, [
                $invitation->team,
                $user,
                $invitation->role,
            ]);

            $merchant = $invitation->team;

            $this->invitations->clearEntity();

            $this->invitations->delete($invitation->id);
        } else {
            $defaultCurrency = null;
            try {
                $defaultCurrency = $this->currencies->findWhereFirst([
                    'name' => Currency::DEFAULT_CURRENCY_NAME,
                ]);
            } catch (\Exception $e) {
                // No default currency
            }

            $merchantData = [
                'name'                  => ($data['company'] ?? 'Merchant #'.time()),
                'slug'                  => ($data['company_slug'] ?? null),
                'website'               => ($data['website'] ?? ''),
                'currency'              => Currency::DEFAULT_CURRENCY_NAME,
                'currency_display_sign' => Currency::DEFAULT_CURRENCY_DISPLAY_SIGN,
            ];

            if ($defaultCurrency) {
                $merchantData['currency_id'] = $defaultCurrency->id;
                $merchantData['currency'] = $defaultCurrency->name;
            }

            $merchant = \Spark::interact(CreateTeam::class, [
                $user,
                $merchantData,
            ]);
        }

        // Create merchant details record
        try {
            $this->merchantDetails->updateOrCreate(['merchant_id' => $merchant->id], [
                'api_key'    => str_random(60),
                'api_secret' => str_random(60),
            ]);
        } catch (\Exception $e) {
            //
        }

        return $merchant;
    }
}