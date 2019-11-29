<?php

namespace App\Http\Controllers;

use App\Repositories\BillingRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\UserRepository;
use App\Models\Billing;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Knp\Snappy\Pdf as PDF;

class AccountController extends Controller
{

	public function __construct()
	{
		$this->merchantRepo = new MerchantRepository;
		$this->billingRepo = new BillingRepository;
		$this->userRepo = new UserRepository;
	}

	public function settings () 
	{
		
		return view('account.settings');
		
	}
	
	public function settingsStore (Request $request) 
	{
		
		Session::flash('success', 'Your Account Settings was successfully saved!');
		return redirect()->back();
		
	}

    public function billing()
    {
        $user = $this->userRepo->current();
        $merchant = $this->merchantRepo->getCurrent();
        $billings = $this->billingRepo->getByMerchant($merchant);
        $plan = $merchant->plan();
        $subscription = $merchant->plan_subscription;
        $planPrice = null;

        if ($plan && $plan->type && ! empty($subscription)) {
            $stripePlans = config('plans');
            $planPeriod = ($subscription && $subscription->length == 365) ? 'yearly' : 'monthly';
            $planPrice = $stripePlans[$plan->type]['price'][$planPeriod];
        }

        return view('account.billing', compact('billings', 'merchant', 'plan', 'user', 'subscription', 'planPrice'));
    }

	public function getBillPdf($bill_id){
		// $merchant = $this->merchantRepo->getCurrent();
		$bill = Billing::find($bill_id);
		$merchant = $bill->merchant;
		$plan = $bill->plan;
		$user = $bill->user;
		$points_settings = $merchant->points_settings;
		if(!empty($points_settings)){
			$currency_sign = $points_settings->currency;
		} else {
			$currency_sign = '$';
		}
		if(empty($bill)){
			abort(500, 'Invalid subscription id');
		}
		$currency_sign = \str_replace('$', '\$', $currency_sign);
		$fileName = 'bill_'. $bill->id .'_'. Carbon::now() .'.pdf';
		$pdfFile = config('services.wkhtmltopdf.storage'). $fileName;
		$pdfConverter = new PDF(config('services.wkhtmltopdf.bin'));
		$pdfConverter->setOption('margin-top', 20);
		$pdfConverter->setOption('margin-bottom', 15);
		$pdfConverter->setOption('margin-left', 15);
		$pdfConverter->setOption('margin-right', 15);
		// dd($currency_sign . $plan->price);
		$html = file_get_contents(base_path() . config('services.wkhtmltopdf.template'));
		$image = public_path(). config('services.wkhtmltopdf.image');
		$html = \preg_replace('/\@image_path/', $image , $html);
		$html = \preg_replace('/\@date/', \Carbon\Carbon::now()->format("m/d/Y") , $html);
		$html = \preg_replace('/\@merchant_name/', $user->first_name .' '. $user->last_name, $html);
		$html = \preg_replace('/\@billing_email/', $user->billing_email , $html);
		$html = \preg_replace('/\@plan_name/', $plan->name ." - ". $bill->period , $html);
		$html = \preg_replace('/\@payment_method/', $user->card_brand .' '. $user->card_last_four, $html);
		$html = \preg_replace('/\@plan_price/', $currency_sign . strval($bill->price), $html);
		$html = \preg_replace('/\@total_price/', $currency_sign . strval($bill->price), $html);
		$pdfConverter->generateFromHtml($html , $pdfFile);
		return response()->download($pdfFile, $fileName, ['Content-Type: application/pdf'])->deleteFileAfterSend(true);
	}

	public function upgrade(){
      /*$merchantRepo = new MerchantRepository();
      $currentPlan = $merchantRepo->getCurrent()->plan()->first();
      if(!isset($currentPlan)){
         $currentPlan = new Plan;
      }*/
      //$plans = Plan::where('type', '!=', 'free')->get()->sortBy('growth_order');
      return view('account.upgrade');
   }
}
