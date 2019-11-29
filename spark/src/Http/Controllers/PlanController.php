<?php

namespace Laravel\Spark\Http\Controllers;

use App\Models\Plan;
use Laravel\Spark\Spark;
use Laravel\Spark\Http\Resources\Plan as PlanResource;

class PlanController extends Controller
{
    /**
     * Get the all of the regular plans defined for the application.
     *
     * @return Response
     */
    public function all()
    {
        return response()->json(Spark::allPlans());
    }

    public function basePlans()
    {
        return PlanResource::collection(Plan::all());
    }
}
