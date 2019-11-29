<?php
namespace App\Jobs\Webhooks\Integrations\Traits;

use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\HasActionWhere;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

trait TrustSpotActionTrait {

    public function handleAction( $customers, $merchantActions, $merchants, $current_action_review_type ) {

        $this->customers = $customers;
        $this->merchantActions = $merchantActions;
        $this->merchants = $merchants;

        if (isset($this->data->lootly_merchant_id) && trim($this->data->lootly_merchant_id)) {
            $merchantId = trim($this->data->lootly_merchant_id);

            try {
                $merchant = $this->merchants->find($merchantId);

                Log::info('TrustSpot action ('.$this->data->data['customer_email'].') for merchant #'.$merchant->id);
                try {

                    // Get customer
                    $customer = $this->customers->findWhereFirst([
                        'merchant_id' => $merchant->id,
                        'email'       => trim($this->data->data['customer_email']),
                    ]);

                    if ($customer) {

                        // Check if TrustSpot action is active
                        $trustSpotActions = $this->merchantActions->withCriteria([
                            new ByMerchant($merchant->id),
                            new EagerLoad(['action']),
                            new HasActionWhere([
                                'type' => 'Store',
                                'url'  => 'trustspot-review',
                            ]),
                        ])->findWhere([
                            'active_flag' => 1,
                        ]);

                        if (count($trustSpotActions)) {

                            $points_credited = false;
                            $all_action_number = null;

                            for ($i = 0; $i < count($trustSpotActions); $i++) {

                                // If find merchant action with current content type - credit points
                                if( $trustSpotActions[$i]->review_type == $current_action_review_type ) {

                                    // Validate and credit points
                                    app('action_service')->creditPointsForAction($trustSpotActions[$i], $customer->id);
                                    $points_credited = true;
                                }

                                // Remember position of "All" content type merchant action
                                if( $trustSpotActions[$i]->review_type == 'all' ) {

                                    $all_action_number = $i;
                                }
                            }

                            // If points were not credited and "All" content type action exists - credit points
                            if( !$points_credited && $all_action_number ) {

                                app('action_service')->creditPointsForAction($trustSpotActions[$all_action_number], $customer->id);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::info('Error during customer processing for merchant #'.$merchant->id.': '.$e->getMessage());
                }
            } catch (\Exception $exception) {
                Log::warning('No merchants with active Trustspot integration');
            }
        } else {
            Log::warning('TrustSpot job: Bad request data');
        }
    }
}
