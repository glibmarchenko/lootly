<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Spark\Spark;

class AuthIntegrationWebhook
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $integration = $request->integration ?: null;

        if (! $integration) {
            abort(500, 'No integration for webhook provided');
        }

        $allowedIntegrations = config('integrations.available_integrations');
        if (! in_array($integration, $allowedIntegrations)) {
            abort(500, "Webhook is not allowed");
        }

        if (!isset(config('integrations')[$integration])) {
            $integration = 'common';
        }

        $webhookMiddlewareClassPath = config('integrations.'.$integration.'.webhook_middleware_class', null);
        if ($webhookMiddlewareClassPath) {
            if (! class_exists($webhookMiddlewareClassPath)) {
                // Can not find a middleware for this webhook type
                abort(500, "Missing webhook middleware: {$webhookMiddlewareClassPath}");
            }

            return $webhookMiddlewareClassPath::verify($request, $next);
        }

        return $next($request);
    }
}
