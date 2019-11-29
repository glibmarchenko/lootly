<?php

namespace App\Jobs\Webhooks\Integrations\Trustspot;

use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\Contracts\MerchantRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Jobs\Webhooks\Integrations\Traits\TrustSpotActionTrait;

class VideoSubmitJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TrustSpotActionTrait;

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

    protected $merchantActions;

    protected $merchants;

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
     *
     * @param \App\Repositories\Contracts\MerchantActionRepository $merchantActions
     *
     * @param \App\Repositories\Contracts\MerchantRepository       $merchants
     *
     * @return void
     */
    public function handle(
        CustomerRepository $customers,
        MerchantActionRepository $merchantActions,
        MerchantRepository $merchants
    ) {
        $this->handleAction( $customers, $merchantActions, $merchants, 'video' );
    }
}
