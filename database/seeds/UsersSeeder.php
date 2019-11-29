<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$user = new \App\User();
    	$user->first_name = "user";
    	$user->last_name = "user";
    	$user->email = "user@user.com";
        $user->password = Hash::make("123123");
        $user->card_brand = "mastercard";
        $user->card_last_four = "0101";
        $user->card_expiration = "04/2019";
    	$user->save();

        // Create a store
        $merchant = new \App\Merchant();
        $merchant->owner_id = $user->id;
        $merchant->name = "First Store";
        $merchant->save();

        $merchantUser = new \App\Models\MerchantUser();
        $merchantUser->user_id = $user->id;
        $merchantUser->merchant_id = $merchant->id;
        $merchantUser->role = "owner";
        $merchantUser->save();

    }
}
