<?php

namespace App\Transformers;

use App\Models\Reward;
use App\Models\Tag;
use League\Fractal\TransformerAbstract;

class RewardTransformer extends TransformerAbstract
{

    protected $availableIncludes = ['merchant_reward'];

    public function transform(Reward $item)
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'slug' => $item->slug,
            'display_text' => $item->display_text,
            'url' => $item->url,
            'type' => $item->type,
            'description' => $item->description,
            'display_order' => $item->display_order,
            'icon' => $item->icon,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
        ];
    }

    public function includeMerchantReward(Reward $reward)
    {
        $merchant_reward = $reward->merchant_reward;

        if(!$merchant_reward){
            return null;
        }

        return $this->item($merchant_reward, new MerchantRewardTransformer);
    }

}