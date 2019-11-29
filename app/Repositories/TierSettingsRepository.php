<?php

namespace App\Repositories;

use App\Models\TierSettings;
use App\Models\Tier;
use App\Repositories\TierBenefitRepository;
use Illuminate\Support\Facades\Log;

class TierSettingsRepository
{
    private $baseQuery;

    public function __construct(MerchantRepository $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
        $this->baseQuery = TierSettings::query();
        $this->tierBenefitRepository = new TierBenefitRepository();
        $this->tierRepository = new TierRepository();
    }

    public function get($merchantId = null)
    {
        if(!$merchantId) {
            $merchantId = $this->merchantRepository->getCurrent()->id;
        }

        return $this->baseQuery
            ->where('merchant_id', '=', $merchantId)
            ->first();
    }

    public function edit($data)
    {
        $merchantObj = null;
        if(!empty($data['merchant_id'])){
            $merchantObj = $this->merchantRepository->find($data['merchant_id']);
        } else {
            if(empty($merchantObj)){
                $merchantObj = $this->merchantRepository->current();
            }
        }
        $currency = '$';
        if(!empty($merchantObj->merchant_currency)){
            $currency = $merchantObj->merchant_currency->currency_sign;
        }
        $data += ['currency' => $currency, 'points' => $merchantObj->points_settings];
        $this->editRequirementText($data, $merchantObj);
        $this->editMultiplierText($data, $merchantObj);
        $this->baseQuery
            ->updateOrCreate(
                ['merchant_id' => $merchantObj->id],
                [
                    'merchant_id' => $merchantObj->id,
                    'program_status' => $data['program_status'],
                    'requirement_type' => $data['requirement_type'],
                    'rolling_period' => $data['rolling_period'],
                ]);
    }

    protected function editMultiplierText($data, $merchantObj){
        $points = ['name' => 'Point', 'namePlural' => 'Points']; //Points definition
        if($data['points']){
            $points['name'] = $data['points']['name'];
            $points['namePlural'] = $data['points']['plural_name'];
        }
        $tiers = Tier::where('merchant_id', '=', $merchantObj->id)->get();
        foreach($tiers as $tier){
            // Rewen multiplier text
            $multiplierText = preg_replace('/{points}/i', $tier->multiplier, $tier->multiplier_text_default);
            $multiplierText = preg_replace('/{points-name}/i', $tier->multiplier > 1 ? $points['namePlural'] : $points['namePlural'] , $multiplierText);
            $multiplierText = preg_replace('/{currency}/i', $data['currency'] , $multiplierText);
            $tier->multiplier_text = $multiplierText;
            $tier->save();
        }
    }

    // !!!WARNING!!!
    // IFHELL BELOW
    protected function editRequirementText($data, $merchantObj)
    {                   
        $points = ['name' => 'Point', 'namePlural' => 'Points']; //Points definition
        if($data['points']){
            $points['name'] = $data['points']['name'];
            $points['namePlural'] = $data['points']['plural_name'];
        }

        $period_array = explode('-', $data['rolling_period']);
        $period_quantity = (int) $period_array[0];
        $period_name = $period_array[1];

        if( $period_quantity != 1 ) {
            $period_name .= 's';
        }
        elseif( $period_name == 'year' ) {
            $period_quantity = 365;
            $period_name = 'days';
        }

        $rolling_period = $period_quantity . ' ' . $period_name;

        /*if ($data['rolling_period'] === '2-year') {
            $yerText = '2 Years';
        } else {
            $yerText = '365 days';
        }*/

        $tiers = Tier::where('merchant_id', '=', $merchantObj->id)->get();
        foreach ($tiers as $tier) {
            if ($data['rolling_period'] === '0') {
                    //<-------Spent------->
                if ($data['requirement_type'] === 'amount-spent') {
                    // $requirementText = '$' . $tier->spend_value . ' spent';
                    if(strpos($tier->requirement_text_default, '{spent-points}') === false) {
                        $spentPattern = preg_replace('/({earned-points})/i', '{currency}$1', $tier->requirement_text_default);
                        $spentPattern = preg_replace('/{earned-points}/i', '{spent-points}', $spentPattern);
                        $spentPattern = preg_replace('/{points-name}/i', '', $spentPattern);
                        $spentPattern = preg_replace('/\s+earned\s+/i', ' spent ', $spentPattern);
                        $tier->requirement_text_default = $spentPattern;
                    }
                    $tier->requirement_text_default = preg_replace('/\s*in\s*the\s*last/i', '', $tier->requirement_text_default);
                    $requirementText = preg_replace('/{spent-points}/i', $tier->spend_value, $tier->requirement_text_default);
                    $requirementText = preg_replace('/{currency}/i', $data['currency'] , $requirementText);
                    $requirementText = preg_replace('/{period}/i', '' , $requirementText);
                } else {
                    //<-------Earned------->
                    // $requirementText = $tier->spend_value .' '. ($tier->spend_value > 1 ? $points['namePlural'] : $points['name']) . ' earned';
                    if(strpos($tier->requirement_text_default, '{earned-points}') === false){
                        $requirementText = $tier->spend_value .' '. ($tier->spend_value > 1 ? $points['namePlural'] : $points['name']) . ' earned in the last ' . $rolling_period;
                        $earnedPattern = preg_replace('/({spent-points})/i', '$1 {points-name}', $tier->requirement_text_default);
                        $earnedPattern = preg_replace('/{spent-points}/i', '{earned-points}', $earnedPattern);
                        $earnedPattern = preg_replace('/{currency}/i', '', $earnedPattern);
                        $earnedPattern = preg_replace('/\s+spent\s+/i', ' earned ', $earnedPattern);
                        $tier->requirement_text_default = $earnedPattern;
                    }
                    $tier->requirement_text_default = preg_replace('/\s*in\s*the\s*last/i', '', $tier->requirement_text_default);
                    $requirementText = preg_replace('/{earned-points}/i', $tier->spend_value, $tier->requirement_text_default);
                    $requirementText = preg_replace('/{points-name}/i', $tier->spend_value > 1 ? $points['namePlural'] : $points['name'] , $requirementText);
                    $requirementText = preg_replace('/{point_name}/i', $tier->spend_value > 1 ? $points['namePlural'] : $points['name'] , $requirementText);
                    $requirementText = preg_replace('/{period}/i', '' , $requirementText);
                }
            } else { // not null period
                // $requirementText = $data['currency'] . $tier->spend_value . ' spent in the last ' . $yerText;
                //<-------Spent------->
                if ($data['requirement_type'] === 'amount-spent') {
                    if(strpos($tier->requirement_text_default, '{spent-points}') === false){
                        $spentPattern = preg_replace('/({earned-points})/i', '{currency}$1', $tier->requirement_text_default);
                        $spentPattern = preg_replace('/{earned-points}/i', '{spent-points}', $spentPattern);
                        $spentPattern = preg_replace('/{points-name}/i', '', $spentPattern);
                        $spentPattern = preg_replace('/\s+earned\s+/i', ' spent ', $spentPattern);
                        $tier->requirement_text_default = $spentPattern;
                    }
                    $pattern = $tier->requirement_text_default;
                    if(strpos($tier->requirement_text_default, 'in the last') === false) {
                        $pattern = preg_replace('/({period})/i', 'in the last $1', $tier->requirement_text_default);
                    }
                    $tier->requirement_text_default = $pattern;
                    $requirementText = preg_replace('/{spent-points}/i', $tier->spend_value, $pattern);
                    $requirementText = preg_replace('/{currency}/i', $data['currency'] , $requirementText);
                    $requirementText = preg_replace('/{period}/i', $rolling_period , $requirementText);
                } else {
                    //<-------Earned------->
                    // $requirementText = $tier->spend_value .' '. ($tier->spend_value > 1 ? $points['namePlural'] : $points['name']) . ' earned in the last ' . $yerText;
                    if(strpos($tier->requirement_text_default, '{earned-points}') === false){
                        $earnedPattern = preg_replace('/({spent-points})/i', '$1 {points-name}', $tier->requirement_text_default);
                        $earnedPattern = preg_replace('/{spent-points}/i', '{earned-points}', $earnedPattern);
                        $earnedPattern = preg_replace('/{currency}/i', '', $earnedPattern);
                        $earnedPattern = preg_replace('/\s+spent\s+/i', ' earned ', $earnedPattern);
                        $tier->requirement_text_default = $earnedPattern;
                    }
                    $pattern = $tier->requirement_text_default;
                    if(strpos($tier->requirement_text_default, 'in the last') === false) {
                        $pattern = preg_replace('/({period})/i', 'in the last $1', $tier->requirement_text_default);
                    }
                    $tier->requirement_text_default = $pattern;
                    $requirementText = preg_replace('/{earned-points}/i', $tier->spend_value, $pattern);
                    $requirementText = preg_replace('/{points-name}/i', $tier->spend_value > 1 ? $points['namePlural'] : $points['name'] , $requirementText);
                    $requirementText = preg_replace('/{point_name}/i', $tier->spend_value > 1 ? $points['namePlural'] : $points['name'] , $requirementText);
                    $requirementText = preg_replace('/{period}/i', $rolling_period , $requirementText);
                }
            }
            $tier->requirement_text = $requirementText;
            $tier->save();
        }
    }

    public function getTierSettingsByMerchantId(int $merchantId)
    {
        $tierSettings = TierSettings::where(['merchant_id' => $merchantId])->first();

        return $tierSettings;
    }
}
