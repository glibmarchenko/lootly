<?php

namespace App\Http\Controllers\Api\Subscriptions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserSubscripsion;
use App\Merchant;

class SubscriptionsController extends Controller
{
    public function checkSubscriptions(Request $request){
        $file = '../check_subscriptions_log.txt';
        foreach(Merchant::all() as $merchant){
            if($merchant->plan_id < 4) { 
                $merchant->rewards->filter(function($value, $key){
                    if($value->reward_type == "Variable amount" && $value->active_flag == 1){ // deactivate Variable amount reward
                        $value->active_flag = 0;
                        $value->save();
                    }
                });
            }
            if($merchant->plan_id < 2){
                $merchant->merchant_actions->filter(function($value, $key){ //deactivate TrustSpot Review and Read Content actions
                    if(($value->action_name == 'Read Content' || $value->action_name == 'TrustSpot - Reviews & UGC') && $value->active_flag == 1){
                        $value->active_flag = 0;
                        $value->save();
                    }
                });
            }
        }
        
        return response()->json(['status' => 200]);
    }
}
