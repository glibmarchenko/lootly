<?php
namespace App\Http\Controllers\Settings\Profile;

use App\Repositories\MerchantRewardRepository;
use App\Repositories\MerchantRepository;
// use App\Http\Settings\Vip\SettingsController;
use App\Repositories\TierSettingsRepository;
use App\Repositories\MerchantActionRepository;

class UpdateCurrencyController {

    public function __construct (){
        $this->merchantRewardRepository = new MerchantRewardRepository();
        $this->merchantRepository = new MerchantRepository();
        $this->tierSettingsRepository = new TierSettingsRepository($this->merchantRepository);
        $this->merchantActionRepository = new MerchantActionRepository();
    }

    public function updateVipCurrency($merchantObj){
        $currentVipSettings = $this->tierSettingsRepository->get();
        if(empty($currentVipSettings)){
            $this->tierSettingsRepository->edit([
                                                'rolling_period' => '1-year', 
                                                'requirement_type' => 'amount-spent',
                                                'program_status' => 'Disabled',
                                                ]);
            $this->merchantRewardRepository->updateTextPatterns([
                                                'rolling_period' => '1-year', 
                                                'requirement_type' => 'amount-spent',
                                                'program_status' => 'Disabled',
                                                ], $merchantObj);
        } else {
            $this->tierSettingsRepository->edit($currentVipSettings->toArray());
            $this->merchantRewardRepository->updateTextPatterns($currentVipSettings->toArray(), $merchantObj); //update currency for rewards
            $this->merchantActionRepository->updateTextPatterns($merchantObj); //update currency for actions
        }
    }

    public function updateAll(){

    }
}