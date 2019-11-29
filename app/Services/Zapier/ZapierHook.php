<?php

namespace App\Services\Zapier;

use App\Merchant;
use App\Models\ZapierHook as Zapier;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ZapierHook
{

    protected $httpClient;

    public function __construct(Client $client)
    {
        $this->httpClient = $client;
    }

    public function send(Merchant $merchant, string $event, array $data)
    {

        $hooks = Zapier::query()
            ->where('user_id', $merchant->id)
            ->where('event', $event)
            ->get();

        foreach ($hooks as $hook) {

            try {
                $this->httpClient->post($hook->url, [
                    'json' => $data
                ]);
            } catch (\Exception $ex) {
                Log::error($ex->getMessage());
            }

        }

    }

}
