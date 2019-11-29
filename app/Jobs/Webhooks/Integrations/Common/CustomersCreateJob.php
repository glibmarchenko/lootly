<?php

namespace App\Jobs\Webhooks\Integrations\Common;

use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\Contracts\MerchantRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

    protected $integrationType;

    protected $customers;

    protected $merchants;

    protected $merchantActions;

    /**
     * Create a new job instance.
     *
     * @param object $data    The webhook data (JSON decoded)
     * @param array  $headers Request headers
     * @param string $integrationType
     */
    public function __construct($data, array $headers, $integrationType = null)
    {
        $this->data = $data;
        $this->headers = $headers;
        $this->integrationType = $integrationType;
    }

    /**
     * Execute the job.
     *
     * @param \App\Repositories\Contracts\CustomerRepository       $customers
     * @param \App\Repositories\Contracts\MerchantRepository       $merchants
     * @param \App\Repositories\Contracts\MerchantActionRepository $merchantActions
     *
     * @return void
     */
    public function handle(
        CustomerRepository $customers,
        MerchantRepository $merchants,
        MerchantActionRepository $merchantActions
    ) {
        $this->merchants = $merchants;
        $this->customers = $customers;
        $this->merchantActions = $merchantActions;

        if (! isset($this->data->lootly_merchant_id)) {
            Log::error("Invalid request data");

            return;
        }

        $merchant = $this->merchants->find($this->data->lootly_merchant_id);

        if ($merchant) {
            $validator = Validator::make((array) $this->data, [
                'id'         => 'required|max:191',
                'email'      => 'required|email',
                'first_name' => 'max:191',
                'last_name'  => 'max:191',
                'birthday'   => 'date',
            ]);

            if (! $validator->fails()) {
                Log::info('Creating/Updating customer #'.$this->data->id.' ('.$this->data->email.') in merchant #'.$merchant->id);
                try {
                    $customerStructure = [
                        'name'         => ($this->data->first_name ?? '').' '.($this->data->last_name ?? ''),
                        'email'        => $this->data->email,
                        'ecommerce_id' => $this->data->id,
                        'birthday'     => '0000-00-00'
                    ];

                    if (!empty($this->data->birthday)) {
                        $customerStructure['birthday'] = Carbon::createFromTimestamp(strtotime($this->data->birthday))
                            ->format('Y-m-d');
                    }

                    if (isset($this->data->default_address)) {
                        if (isset($this->data->default_address['country']) && trim($this->data->default_address['country'])) {
                            $customerStructure['country'] = trim($this->data->default_address['country']);
                        }
                        if (isset($this->data->default_address['zip']) && trim($this->data->default_address['zip'])) {
                            $customerStructure['zipcode'] = trim($this->data->default_address['zip']);
                        }
                    }

                    // Create/Update customer
                    $customer = app('customer_service')->updateOrCreate($merchant, $customerStructure);
                } catch (\Exception $e) {
                    Log::info('Error during customer processing for merchant #'.$merchant->id.': '.$e->getMessage());
                }
            } else {
                Log::error('Invalid request data: '.$validator->errors()->first());
            }
        } else {
            Log::error('Merchant with id '.$this->data->lootly_merchant_id.' not found.');
        }
    }
}
