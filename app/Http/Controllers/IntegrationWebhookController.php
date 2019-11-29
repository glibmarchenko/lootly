<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IntegrationWebhookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.integration.webhook');
    }

    /**
     * Handles an incoming webhook.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $integration The type of integration
     * @param string                   $type        The type of webhook
     *
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, $integration, $type)
    {

        $origIntegration = $integration;

        // Check integration config
        if (in_array($integration, config('integrations.available_integrations'))) {
            if (! isset(config('integrations')[$integration])) {
                $integration = 'common';
            }
        }

        try {
            $classPath = $this->getIntegrationJobClassFromType($integration, $type);

            if (! class_exists($classPath)) {
                $classPath = $this->getIntegrationJobClassFromType('common', $type);

                if (! class_exists($classPath)) {
                    // Can not find a job for this webhook type
                    abort(500, "Missing webhook job: {$classPath}");
                }
            }
            // Dispatch
            $data = (object) $request->all();

            $headers = [];

            try {
                $headers = request()->headers->all();
            } catch (\Exception $e) {
                Log::error('Webhook ('.$origIntegration.'/'.$type.'): '.$e->getMessage().'. Can\'t get request headers.');
            }


            // Send Status 200 OK
            $this->respondOK();

            // Continue webhook processing
            dispatch(new $classPath($data, $headers, $origIntegration));
        } catch (\Exception $e) {
            //Log::error('Webhook ('.$origIntegration.'/'.$type.'): '.$e->getMessage().' on line '.$e->getLine());
        }


        return response('', 201);
    }

    public function verifyKey(Request $request, $integration)
    {
        return response()->json([], 200);
    }

    /**
     * Converts type into a class string.
     *
     * @param string $type The type of webhook
     *
     * @return string
     */
    protected function getIntegrationJobClassFromType($integration, $type)
    {
        return '\\App\\Jobs\\Webhooks\\Integrations\\'.str_replace('-', '', ucwords($integration, '-')).'\\'.str_replace('-', '', ucwords($type, '-')).'Job';
    }
}
