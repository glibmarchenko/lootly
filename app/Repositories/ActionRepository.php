<?php

namespace App\Repositories;

use App\Models\Action;
use App\Contracts\Repositories\ActionRepository as ActionRepositoryContract;


class ActionRepository implements ActionRepositoryContract
{
    private $baseQuery;

    public function __construct()
    {
        $this->baseQuery = Action::query();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function get()
    {
        $merchant = new MerchantRepository();
        $currentMerchant = $merchant->getCurrent();
        if (!$currentMerchant) {
            return Action::query()
                ->orderBy('actions.display_order', 'asc')
                ->get();
        }
        $merchantAction = $this->baseQuery
            ->join('merchant_actions', 'merchant_actions.action_id', '=', 'actions.id')
            ->where('merchant_actions.merchant_id', '=', $currentMerchant->id)
            ->pluck('actions.id');
        $action = Action::query()
            ->whereNotIn('id', $merchantAction)
            ->orWhere('url','trustspot-review')
            ->orderBy('actions.display_order', 'asc')
            ->get();
        return $action;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function getType()
    {
        $merchant = new MerchantRepository();
        $currentMerchant = $merchant->getCurrent();
        if (!$currentMerchant) {
            return Action::query()
                ->with('merchantAction')
                ->orderBy('actions.display_order', 'asc')
                ->groupBy('type')
                ->get();
        }
        $merchantAction = Action::query()
            ->join('merchant_actions', 'merchant_actions.action_id', '=', 'actions.id')
            ->where('merchant_actions.merchant_id', '=', $currentMerchant->id)
//            ->orderBy('actions.display_order', 'desc')
            ->pluck('actions.id');
        return Action::query()
            ->with('merchantAction')
            ->whereNotIn('id', $merchantAction)
            ->orderBy('actions.display_order', 'asc')
            ->groupBy('type')
            ->get();

    }


    public function findByName($name)
    {
        $merchant = new MerchantRepository();
        $currentMerchant = $merchant->getCurrent();
        $action = $this->baseQuery->with(array('merchantAction'=>function($query) use($currentMerchant){
            $query->where('merchant_id', '=', $currentMerchant->id);
        }))->where('actions.name', '=', $name)->first();
        return $action;

    }

    public function findByUrl($url) {
        return Action::query()->where('url', '=', $url)->first();
    }


}
