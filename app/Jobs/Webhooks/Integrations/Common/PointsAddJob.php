<?php

namespace App\Jobs\Webhooks\Integrations\Common;

use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\IntegrationRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\PointRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PointsAddJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The webhook data.
     *
     * @var object
     */
    protected $data;

    /**
     * Request headers.
     *
     * @var string
     */
    protected $headers;

    protected $integrationType;

    protected $integrations;

    protected $merchantActions;

    protected $customers;

    protected $merchants;

    protected $points;

    /**
     * PointsAddJob constructor.
     * @param $data
     * @param array $headers
     * @param null $integrationType
     */
    public function __construct($data, array $headers, $integrationType = null )
    {
        $this->data = $data;
        $this->headers = $headers;
        $this->integrationType = $integrationType;
    }

    /**
     * Execute the job.
     *
     * @param MerchantRepository $merchants
     * @param IntegrationRepository $integrations
     * @param MerchantActionRepository $merchantActions
     * @param CustomerRepository $customers
     * @param PointRepository $points
     * @return mixed
     */
    public function handle(
        MerchantRepository $merchants,
        IntegrationRepository $integrations,
        MerchantActionRepository $merchantActions,
        CustomerRepository $customers,
        PointRepository $points
    ) {
        $this->merchants = $merchants;
        $this->integrations = $integrations;
        $this->merchantActions = $merchantActions;
        $this->customers = $customers;
        $this->points = $points;

        try {
            $customer = $this->customers->findWhereFirst([
                'ecommerce_id' => $this->data->customer_id,
                'merchant_id'  => $this->data->lootly_merchant_id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Not authorized action'], 403);
        }

        if( $customer ) {

            $pointStructure = [
                'merchant_id'             => $this->data->lootly_merchant_id,
                'customer_id'             => $customer->id,
                'point_value'             => $this->data->point_value,
                'rollback'                => 0,
                'title'                   => 'Custom action',
                'reason'                  => '',
                'merchant_action_id'      => null,
                'merchant_reward_id'      => null,
                'coupon_id'               => null,
                'order_id'                => null,
                'total_order_amount'      => 0,
                'rewardable_order_amount' => 0,
                'type'                    => 'Custom',
                'expiration_date'         => null,
                'tier_multiplier'         => 1,
                'referral_id'             => null
            ];

            try {
                $this->points->create($pointStructure);
            } catch (\Exception $e) {
                Log::error( $e->getMessage().' on line '.$e->getLine() );
            }
        }

    }
}
