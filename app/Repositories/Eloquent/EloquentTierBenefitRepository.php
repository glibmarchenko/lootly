<?php

namespace App\Repositories\Eloquent;

use App\Models\TierBenefit;
use App\Repositories\Contracts\TierBenefitRepository;
use App\Repositories\RepositoryAbstract;
use Illuminate\Support\Facades\Log;

class EloquentTierBenefitRepository extends RepositoryAbstract implements TierBenefitRepository
{
    public function entity()
    {
        return TierBenefit::class;
    }

    public function createMany($tierId, array $benefits)
    {
        foreach ($benefits as $benefit_type => $benefit) {
            foreach ($benefit as $benefit_item) {
                if (isset($benefit_item['discount']) && ! empty($benefit_item['discount'])) {
                    if ($benefit_type == 'custom' || ($benefit_type == 'entry' && $benefit_item['reward'] == 'points')) {
                        $this->create([
                            'tier_id'            => $tierId,
                            'merchant_reward_id' => null,
                            'benefits_type'      => $benefit_type,
                            'benefits_discount'  => $benefit_item['discount'],
                            'benefits_reward'    => (isset($benefit_item['reward']) && ! empty($benefit_item['reward'])) ? $benefit_item['reward'] : null,
                        ]);
                        $this->clearEntity();
                    } else {
                        if (isset($benefit_item['reward']) && ! empty($benefit_item['reward'])) {
                            $this->create([
                                'tier_id'            => $tierId,
                                'merchant_reward_id' => $benefit_item['id'],
                                'benefits_type'      => $benefit_type,
                                'benefits_discount'  => $benefit_item['discount'],
                                'benefits_reward'    => (isset($benefit_item['reward']) && ! empty($benefit_item['reward'])) ? $benefit_item['reward'] : null,
                            ]);
                            $this->clearEntity();
                        }
                    }
                }
            }
        }
    }
}