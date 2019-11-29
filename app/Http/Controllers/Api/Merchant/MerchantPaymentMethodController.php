<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Merchant;
use App\Repositories\Contracts\MerchantRepository;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Token as StripeToken;
use Stripe\Customer as StripeCustomer;
use Stripe\Card as StripeCard;
use Stripe\BankAccount as StripeBankAccount;

class MerchantPaymentMethodController extends Controller
{
    protected $merchants;

    public function __construct(MerchantRepository $merchants)
    {
        $this->merchants = $merchants;
    }

    public function update(Request $request, Merchant $merchant)
    {
        $request->validate([
            'stripe_token' => 'required',
        ]);

        $user = $request->user();

        $data = $request->all();

        if ($merchant->stripe_id) {
            $this->updateCard($merchant, $data['stripe_token']);
        } else {
            $this->createAsStripeCustomer($user, $merchant, $data['stripe_token']);
        }
    }

    protected function updateCard(Merchant $merchant, $token)
    {
        $customer = StripeCustomer::retrieve($merchant->stripe_id, config('services.stripe.secret'));

        $token = StripeToken::retrieve($token, ['api_key' => config('services.stripe.secret')]);

        // If the given token already has the card as their default source, we can just
        // bail out of the method now. We don't need to keep adding the same card to
        // a model's account every time we go through this particular method call.
        if ($token[$token->type]->id === $customer->default_source) {
            return;
        }

        $card = $customer->sources->create(['source' => $token]);

        $customer->default_source = $card->id;

        $customer->save();

        // Next we will get the default source for this model so we can update the last
        // four digits and the card brand on the record in the database. This allows
        // us to display the information on the front-end when updating the cards.
        $source = $customer->default_source ? $customer->sources->retrieve($customer->default_source) : null;

        $card_brand = '';
        $card_last_four = '';
        $card_expiration = '';

        if ($source instanceof StripeCard) {
            $card_brand = $source->brand;
            $card_last_four = $source->last4;
            if (isset($source->exp_month) && isset($source->exp_year)) {
                $card_expiration = sprintf("%02d", intval($source->exp_month)).'/'.substr($source->exp_year, -2, 2);
            }
        } elseif ($source instanceof StripeBankAccount) {
            $card_brand = 'Bank Account';
            $card_last_four = $source->last4;
            if (isset($source->exp_month) && isset($source->exp_year)) {
                $card_expiration = sprintf("%02d", intval($source->exp_month)).'/'.substr($source->exp_year, -2, 2);
            }
        }

        $this->merchants->clearEntity();
        $this->merchants->update($merchant->id, [
            'card_brand'     => $card_brand,
            'card_last_four' => $card_last_four,
            'card_expiration' => $card_expiration,
        ]);
    }

    protected function createAsStripeCustomer(User $user, Merchant $merchant, $token)
    {
        $options = ['email' => $user->email];

        // Here we will create the customer instance on Stripe and store the ID of the
        // user from Stripe. This ID will correspond with the Stripe user instances
        // and allow us to retrieve users from Stripe later when we need to work.
        $customer = StripeCustomer::create($options, config('services.stripe.secret'));

        $this->merchants->clearEntity();
        $this->merchants->update($merchant->id, [
            'stripe_id' => $customer->id,
        ]);

        $merchant->stripe_id = $customer->id;

        // Next we will add the credit card to the user's account on Stripe using this
        // token that was provided to this method. This will allow us to bill users
        // when they subscribe to plans or we need to do one-off charges on them.
        if (! is_null($token)) {
            $this->updateCard($merchant, $token);
        }

        return $customer;
    }
}