<?php

namespace App\Repositories;

use App\Models\Reward;
use App\Models\MerchantReward;
use App\Contracts\Repositories\RewardRepository as RewardRepositoryContract;


class RewardRepository implements RewardRepositoryContract
{
    private $baseQuery;

    public function __construct()
    {
        $this->baseQuery = Reward::query();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function get()
    {
        $merchant = new MerchantRepository();
        $currentMerchant = $merchant->getCurrent();

        return $this->baseQuery
            ->where('type', '!=', 'Points')     // Disable points
            ->orderBy('rewards.display_order', 'asc')
            ->get();
    }

    public function findByName($name)
    {
        return $this->baseQuery->where('name', '=', $name)->with('merchantReward')->first();
    }

    public function findByRewardTypeId($merchantObj,$name,$rewardTypeId){
        return $this->baseQuery->with(array('merchantReward' => function($query) use($rewardTypeId,$merchantObj)
        {$query->where('type_id','=', $rewardTypeId);
               $query->where('merchant_id','=', $merchantObj->id);
        }))->where('rewards.name','=', $name)->first();
    }

    public function getMerchantRewards()
    {
        $merchant = new MerchantRepository();
        $merchantObj = $merchant->getCurrent();

//        return $this->baseQuery
//            ->where('type', '!=', 'Variable amount')
//            ->orderBy('rewards.display_order', 'asc')
//            ->with('merchantReward')
//            ->get();
        $rewardTypeId=MerchantReward::REWARD_TYPE_POINT;

        return $this->baseQuery->with(array('merchantReward' => function($query) use($rewardTypeId,$merchantObj)
        {$query->where('type_id','=', $rewardTypeId);
            $query->where('merchant_id','=', $merchantObj->id);
        }))    ->where('type', '!=', 'Variable amount')
            ->orderBy('rewards.display_order', 'asc')->get();
    }

    public function getByTypeId($typeId){
        return $this->baseQuery->with(array('merchantReward' => function($query) use($typeId){
            $query->where('type_id', '=', $typeId);
        }))->get();
    }

}
