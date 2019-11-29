<?php

namespace App\Jobs;

use App\Merchant;
use App\Helpers\EcommerceIntegration\BigcommerceEcommerceIntegrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class BigcommerceWebhookInstaller implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $merchant;
    protected $webhooks;
    protected $access_token;
    protected $hash;

    protected  $bcService;


    public function __construct(Merchant $merchant, array $webhooks, string  $access_token, string  $hash, BigcommerceEcommerceIntegrationService $bcService)
    {
        $this->merchant = $merchant;
        $this->webhooks = $webhooks;
        $this->bcService = $bcService;
        $this->access_token = $access_token;
        $this->hash = $hash;
    }

    /**
     * Execute the job.
     *
     * @return array
     */
    public function handle()
    {
        Log::info('Job[BigcommerceWebhookInstall]: Status => Started');

        // Keep track of whats created
        $created = [];

        foreach ($this->webhooks as $webhook) {
            // Check if the required webhook exists on the shop
            try {

                $post = json_encode( $webhook );

                $this->bcService->makeApiCall( 'v2/hooks', $post, $this->hash, $this->access_token );

                $created[] = $webhook;
            } catch (\Exception $e) {
                Log::error('Job[BigcommerceWebhookInstall]: Error => ('.$webhook['topic'].') '.$e->getMessage());
            }
        }

        Log::info('Job[BigcommerceWebhookInstall]: Status => Completed');

        return $created;
    }
}
