<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\MerchantRepository;
use App\Models\Plan;
use App\Models\Subscription;
use App\Merchant;
use Carbon\Carbon;

class PaidPermissionsController extends Controller
{
    /**
     * Check if current user have permissions for the $type_code and return upsell data on fail
     * 
     * @return App\Models\PaidPermission|true
     */
    public function havePermission(Request $request, $type_code){
        if(!isset($type_code)){
            return abort(403, 'Empty type code');
        }
        $merchantRepository = new MerchantRepository;
        $merchant = $merchantRepository->getCurrent();
        if($merchant->checkPermitionByTypeCode($type_code)){
            return response()->json(['message' => 'The merchant has permitions'], 200);
        } else {
            return abort(404, 'This merchant has no permitions');
        }
    }

    public function changePlan($merchant_id, $plan_id){
        if(env('APP_STATUS') != 'dev'){
            return response()->json(['message' => 'Only for dev'], 500);
        }
        $plan = Plan::find($plan_id);
        if(!isset($plan)){
            $plan = new Plan;
        }
        try{
            $merchant = Merchant::findOrFail($merchant_id);
        } catch(\Exeption $e){
            return response()->json([['message' => 'Merchant none found'], 404]);
        }
        $subscription = $merchant->plan_subscription;
        if(empty($subscription)){
            $subscription = new Subscription;
            $subscription->user_id = $merchant->owner->id;
            $subscription->stripe_product_id = -1;
            $subscription->stripe_customer_id = -1;
            $subscription->merchant_id = $merchant_id;
            $subscription->name = 'Test subscription';
            $subscription->ends_at = new Carbon('next month');
        }
        $subscription->plan_id = $plan_id;
        $subscription->save();
        return response()->json(['message' => 'Your current plan: '.$plan->name], 200);
    }
}
