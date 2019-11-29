<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class DemoDataContoller extends Controller
{
    public function customers(){
		$names = ['Ryan Haidinger', 'Larry Rex', 'Bonnie V', 'Aria R'];
		$total_spend = [250, 500, 1000];
		$points_earned = [200, 500, 1000];
		$vip_tier = ['Gold', 'Bronze', 'Silver'];
		$date = [ Carbon::now()->toDateTimeString(), Carbon::yesterday()->toDateTimeString(), Carbon::now()->subYear()->toDateTimeString(), Carbon::now()->subMonth()->toDateTimeString() ];
		
		$data = array();
		for($i = 0; $i < 25; $i++){
			 $data[] = [
				 'name' => $names[array_rand($names)],
				 'purchases' => $i+1,
				 'total_spend' => $total_spend[array_rand($total_spend)],
				 'points' => $points_earned[array_rand($points_earned)],
				 'vip_tier' => $vip_tier[array_rand($vip_tier)],
				 'date' => $date[array_rand($date)]
			 ];
		}
		return $data;		
    }

    public function pointsActivity()
	{
		$names = ['Ryan Haidinger', 'Larry Rex', 'Bonnie V'];
		$activity = ['Placed an order', 'Celebrated birthday', 'Redeemed for 10% off', 'Visited blog', 'Left a review'];
		$points = [200, -500, 1000, 90, -200];
		$date = [ Carbon::now()->toDateTimeString(),  Carbon::now()->subHours(6)->toDateTimeString(), Carbon::yesterday()->toDateTimeString(), Carbon::now()->subYear()->toDateTimeString(), Carbon::now()->subMonth()->toDateTimeString() ];
		
		$data = array();
		for($i = 0; $i < 25; $i++){
			 $data[] = [
				 'name' => $names[array_rand($names)],
				 'activity' => $activity[array_rand($activity)],
				 'points' => $points[array_rand($points)],
				 'date' => $date[array_rand($date)]
			 ];
		}
		return $data;
				
	}

    public function vipActivity()
	{
		$names = ['Ryan Haidinger', 'Larry Rex', 'Bonnie V'];
		$activity = ['Downgraded Tier', 'Upgraded Tier', 'Admin Upgrade', 'New VIP Member'];
		$tier = ['Bronze', 'Silver', 'Gold'];
		$date = [ Carbon::now()->toDateTimeString(),  Carbon::now()->subHours(6)->toDateTimeString(), Carbon::yesterday()->toDateTimeString(), Carbon::now()->subYear()->toDateTimeString(), Carbon::now()->subMonth()->toDateTimeString() ];
		
		$data = array();
		for($i = 0; $i < 25; $i++){
			 $data[] = [
				 'name' => $names[array_rand($names)],
				 'activity' => $activity[array_rand($activity)],
				 'current_tier' => $tier[array_rand($tier)],
				 'previous_tier' => $tier[array_rand($tier)],
				 'date' => $date[array_rand($date)]
			 ];
		}
		return $data;
				
	}

    public function referralsActivity()
	{
		$names = ['Ryan Haidinger', 'Larry', 'Bonnie'];
		$referredNames = ['Ryan', 'Larry', 'Bonnie', 'Brad', 'Ryan'];
		$orderNumbers = ['1000', '1100', '1105', '1005'];
		$orderTotals = ['199', '299', '399', '499'];
		$date = [ Carbon::now()->toDateTimeString(),  Carbon::now()->subHours(6)->toDateTimeString(), Carbon::yesterday()->toDateTimeString(), Carbon::now()->subYear()->toDateTimeString(), Carbon::now()->subMonth()->toDateTimeString() ];
		
		$data = array();
		for($i = 0; $i < 20; $i++){
			 $data[] = [
				 'name' => $names[array_rand($names)],
				 'referred_name' => $referredNames[array_rand($referredNames)],
				 'order_number' => $orderNumbers[array_rand($orderNumbers)],
				 'order_total' => $orderTotals[array_rand($orderTotals)],
				 'date' => $date[array_rand($date)]
			 ];
		}
		return $data;
				
	}

    public function billing()
	{
		$description = ['Growth Monthly'];
		$amount = ['59', '29', '39', '49'];
		$date = [ 
			Carbon::now()->toDateTimeString(),
			Carbon::yesterday()->toDateTimeString(),
			Carbon::now()->subYear()->toDateTimeString(),
			Carbon::now()->subMonth()->toDateTimeString() 
		];
		
		$data = array();
		for($i = 0; $i < 20; $i++){
			 $data[] = [
			 	'id' => $i+1,
				'description' => $description[array_rand($description)],
				'amount' => $amount[array_rand($amount)],
				'date' => $date[array_rand($date)]
			 ];
		}
		return $data;
				
	}

    public function reportsReferrers()
	{
		$email = ['ryan@trustspot.io', 'larry@trustspot.io', 'page@trustspot.io', 'max@trustspot.io'];
		$shares = ['50', '100', '200', '300', '500'];
		$clicks = ['50', '100', '200', '300', '500'];
		$orders = ['500', '850', '950', '1000', '240'];
		$avg_order = ['18.5', '8.45', '89.95', '19.99', '75.00'];
		$revenue = ['18000', '17800', '10500', '2000', '100'];
		
		$data = array();
		for($i = 0; $i < 25; $i++){
			 $data[] = [
			 	'id' => $i+1,
				'email' => $email[array_rand($email)],
				'shares' => $shares[array_rand($shares)],
				'clicks' => $clicks[array_rand($clicks)],
				'orders' => $orders[array_rand($orders)],
				'avg_order' => $avg_order[array_rand($avg_order)],
				'revenue' => $revenue[array_rand($revenue)]
			 ];
		}
		return $data;
				
	}

    public function popularEarning()
	{
		$name = ['Make a Purchase', 'Facebook Like', 'Twitter Like', 'Facebook Share', 'Twitter Share'];
		$reward = ['1 Point for every $1 spent', '25 Points', '50 Points', '100 Points', '500 Points'];
		$points_earned = ['30000', '28800', '1520', '22200', '10110'];
		$completed_action = ['3000', '2800', '1500', '2000', '100'];
		
		$data = array();
		for($i = 0; $i < 25; $i++){
			$temp = $name[array_rand($name)];
			 $data[] = [
			 	'id' => $i+1,
				'name' => $temp,
				'action_type' => $temp,
				'reward' => $reward[array_rand($reward)],
				'points_earned' => $points_earned[array_rand($points_earned)],
				'completed_action' => $completed_action[array_rand($completed_action)]
			 ];
		}
		return $data;
				
	}

    public function popularSpending()
	{
		$name = [
			['$10 off discount', 'Fixed amount discount'],
			['$25 off Shipping', 'Free Shipping'],
			['Product A', 'Free Product'],
			['30% off discount', 'Percentage discount'],
			['Product B', 'Free Product']
		];

		$reward = ['30000', '28800', '1520', '22200', '10110'];
		$points = ['3000', '2800', '1500', '2000', '100'];
		
		$data = array();
		for($i = 0; $i < 25; $i++){
			$temp = $name[array_rand($name)];
			 $data[] = [
			 	'id' => $i+1,
				'name' => $temp[0],
				'reward_type' => $temp[1],
				'points_required' => $points[array_rand($points)],
				'rewards_issued' => $reward[array_rand($reward)],
				'redemption_count' => $points[array_rand($points)]
			 ];
		}
		return $data;
				
	}

}
