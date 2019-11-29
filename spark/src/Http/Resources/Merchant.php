<?php

namespace Laravel\Spark\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Spark\Http\Resources\User as UserResource;
use Laravel\Spark\Http\Resources\Plan as PlanResource;
use Laravel\Spark\Http\Resources\Integration as IntegrationResource;

class Merchant extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'owner' => new UserResource($this->owner),
            'integrations' => IntegrationResource::collection($this->integrations),
            'plan' => new PlanResource($this->plan()),
        ];
    }
}
