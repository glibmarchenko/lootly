<?php

namespace App\Http\Controllers\Api\Plan;

use App\Repositories\Contracts\PlanRepository;
use App\Transformers\PlanTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PlanController extends Controller
{
    protected $plans;

    public function __construct(PlanRepository $plans)
    {
        $this->plans = $plans;
    }

    public function getPlans()
    {
        $plans = $this->plans->all()->sortBy('growth_order');

        return fractal($plans)->transformWith(new PlanTransformer())->toArray();
    }

    public function getPlansWithFeatures()
    {
        $plans = $this->plans->all()->sortBy('growth_order');

        for ($i = 0; $i < count($plans); $i++) {
            $plans[$i]->features = new \stdClass();
            $plans[$i]->features->title = ($i == 0) ? 'Includes these features:' : 'Everything in '.$plans[$i - 1]->name.' and...';
            $plans[$i]->features->items = $plans[$i]->getUniqueFeatures();
        }

        return fractal($plans)->transformWith(new PlanTransformer())->toArray();
    }

    public function accept(Request $request){
        Log::info('Shopify pricing callback: ');
        Log::info(print_r($request->all(), true));
        Log::info(print_r($request->headers, true));
    }
}
