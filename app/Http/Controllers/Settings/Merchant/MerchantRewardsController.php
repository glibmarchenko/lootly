<?php

namespace App\Http\Controllers\Settings\Merchant;

use App\Transformers\MerchantRewardTransformer;
use App\Http\Controllers\Controller;
use App\Repositories\MerchantRepository;
use App\Repositories\MerchantRewardRepository;

class MerchantRewardsController extends Controller
{
    protected $merchantRepo;
    protected $merchantRewardRepo;
    protected $currentMerchant;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MerchantRepository $merchantRepository, MerchantRewardRepository $merchantRewardRepository)
    {
        $this->merchantRepo = $merchantRepository;
        $this->merchantRewardRepo = $merchantRewardRepository;

        $this->middleware('auth');
        $this->middleware(function($request, $next){
            $this->currentMerchant = $this->merchantRepo->getCurrent();
            if(!$this->currentMerchant){
                abort(401, 'Please add account (merchant)');
            }
            return $next($request);
        });
    }


    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        $merchantRewards = $this->merchantRewardRepo->all($this->currentMerchant);

        if(!$merchantRewards){
            return response()->json([], 201);
        }

        return fractal()->collection($merchantRewards)->transformWith(new MerchantRewardTransformer)->toArray();
    }
}
