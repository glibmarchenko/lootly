<?php

namespace App\Repositories;


use App\Models\TierBenefit;

class TierBenefitRepository
{
    private $baseQuery;

    /**
     * TierRepository constructor.
     */
    public function __construct()
    {
        $this->baseQuery = TierBenefit::query();
    }


    public function add($tier_id, $benefits)
    {
        // dd($tier_id);
        foreach ($benefits as $benefit_type => $benefit) {
            foreach ($benefit as $benefit_item) {
                if(isset($benefit_item['discount']) && !empty($benefit_item['discount'])){
                    if ($benefit_type == 'custom' || ($benefit_type == 'entry' && $benefit_item['reward'] == 'points')) {
                        $this->baseQuery
                            ->create(
                                [
                                    'tier_id' => $tier_id,
                                    'merchant_reward_id' => null,
                                    'benefits_type' => $benefit_type,
                                    'benefits_discount' => $benefit_item['discount'],
                                    'benefits_reward' => (isset($benefit_item['reward']) && !empty($benefit_item['reward'])) ? $benefit_item['reward'] : null,
                                ]
                            );
                    } else {
                        if (isset($benefit_item['reward']) && !empty($benefit_item['reward'])){
                            $this->baseQuery
                                ->create(
                                    [
                                        'tier_id' => $tier_id,
                                        'merchant_reward_id' => $benefit_item['id'],
                                        'benefits_type' => $benefit_type,
                                        'benefits_discount' => $benefit_item['discount'],
                                        'benefits_reward' => (isset($benefit_item['reward']) && !empty($benefit_item['reward'])) ? $benefit_item['reward'] : null,
                                    ]
                                );
                        }
                    }
                }

            }
        }
    }

    public function removeAll($tier_id)
    {

        $this->baseQuery
            ->where('tier_id','=', $tier_id)
            ->delete();
    }

    public function getAll($tier_id)
    {
        return $this->baseQuery->where('tier_id', '=', $tier_id)->get();
    }




}
