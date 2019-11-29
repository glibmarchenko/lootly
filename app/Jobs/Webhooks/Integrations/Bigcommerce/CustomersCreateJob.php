<?php

namespace App\Jobs\Webhooks\Integrations\Bigcommerce;

use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\IntegrationRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\MerchantRepository;
use App\Helpers\EcommerceIntegration\BigcommerceEcommerceIntegrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CustomersCreateJob implements ShouldQueue
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

    //protected $integrationType;

    protected $customers;

    protected $merchantActions;

    protected $integrations;

    /**
     * Create a new job instance.
     *
     * @param object $data    The webhook data (JSON decoded)
     * @param array  $headers Request headers
     * @param string $integrationType
     */
    public function __construct($data, array $headers, $integrationType = null )
    {
        $this->data = $data;
        $this->headers = $headers;
    }

    /**
     * Execute the job.
     *
     * @param \App\Repositories\Contracts\CustomerRepository       $customers
     *
     * @param \App\Repositories\Contracts\MerchantActionRepository $merchantActions
     *
     * @param \App\Repositories\Contracts\IntegrationRepository    $integrations
     *
     * @return void
     */
    public function handle(
        CustomerRepository $customers,
        MerchantActionRepository $merchantActions,
        IntegrationRepository $integrations,
        BigcommerceEcommerceIntegrationService $bcService,
        MerchantRepository $merchantModel
    ) {
        $this->customers = $customers;
        $this->merchantActions = $merchantActions;
        $this->integrations = $integrations;
        $this->bcService = $bcService;
        $this->merchantModel = $merchantModel;

        $shopDomain = $this->data->producer;

        $merchant = $bcService->getMerchant( $shopDomain );

        if ( $merchant ) {

            $bigcommerceIntegration = $this->bcService->getBigcommerceIntegration();
            $checkIntegration = $merchantModel->findIntegrationWithToken( $merchant, $bigcommerceIntegration );
            $token = trim($checkIntegration->pivot->token);

            $customer_info = $this->bcService->makeApiCall( 'v2/customers/' . $this->data->data['id'], null, $shopDomain, $token );

            Log::info('Creating/Updating customer #');

            $customerStructure = [
                'name'         => $customer_info->first_name . ' ' . $customer_info->last_name,
                'email'        => $customer_info->email,
                'ecommerce_id' => $customer_info->id,
                'birthday'     => '0000-00-00'
            ];

            // Create/Update customer
            $customer = app('customer_service')->updateOrCreate($merchant, $customerStructure);

        } else {
            Log::warning('No merchants with active Bigcommerce integration');
        }
    }
}
