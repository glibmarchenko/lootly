<?php

namespace App\Helpers\EcommerceIntegration\ApiClient;

use Closure;
use Exception;
use GuzzleHttp\Client;

class CommonApiClient
{
    /**
     * The Guzzle client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The shop domain.
     *
     * @var string
     */
    protected $shop;

    /**
     * The API key.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * The API secret.
     *
     * @var string
     */
    protected $apiSecret;

    /**
     * If API calls are from a public or private app.
     *
     * @var string
     */
    protected $private;

    public function __construct(bool $private = true)
    {
        // Set if app is private or public
        $this->private = $private;

        // Create a default Guzzle client
        $this->client = new Client();

        return $this;
    }

    /**
     * Determines if the calls are private.
     *
     * @return bool
     */
    public function isPrivate()
    {
        return $this->private === true;
    }

    /**
     * Determines if the calls are public.
     *
     * @return bool
     */
    public function isPublic()
    {
        return ! $this->isPrivate();
    }

    /**
     * Sets the Guzzle client for the API calls (allows for override with your own).
     *
     * @param \GuzzleHttp\Client $client The Guzzle client
     *
     * @return self
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Sets the shop domain we're working with.
     *
     * @param string $shop The shop domain
     *
     * @return self
     */
    public function setShop(string $shop)
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Gets the shop domain we're working with.
     *
     * @return string
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * Sets the API key for use with the Shopify API (public or private apps).
     *
     * @param string $apiKey The API key
     *
     * @return self
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Sets the API secret for use with the Shopify API (public apps).
     *
     * @param string $apiSecret The API secret key
     *
     * @return self
     */
    public function setApiSecret(string $apiSecret)
    {
        $this->apiSecret = $apiSecret;

        return $this;
    }

    /**
     * Simple quick method to set shop and api key\secret in one shot.
     *
     * @param string $shop      The shop's domain
     * @param string $apiKey    The API key
     * @param string $apiSecret The API secret
     *
     * @return self
     */
    public function setSession(string $shop, string $apiKey, string $apiSecret)
    {
        $this->setShop($shop);
        $this->setApiKey($apiKey);
        $this->setApiSecret($apiSecret);

        return $this;
    }

    /**
     * Accepts a closure to do isolated API calls for a shop.
     *
     * @param string  $shop      The shop's domain
     * @param string  $apiKey    The API key
     * @param string  $apiSecret The API secret
     * @param Closure $closure   The closure to run isolated
     *
     * @throws \Exception When closure is missing or not callable
     *
     * @return self
     */
    public function withSession(string $shop, string $apiKey, string $apiSecret, Closure $closure)
    {
        // Clone the API class and bind it to the closure
        $clonedApi = clone $this;
        $clonedApi->setSession($shop, $apiKey, $apiSecret);

        return $closure->call($clonedApi);
    }

    /**
     * Alias for REST method for backwards compatibility.
     *
     * @see rest
     */
    public function request()
    {
        return call_user_func_array([
            $this,
            'rest',
        ], func_get_args());
    }

    /**
     * Runs a request to the shop API.
     *
     * @param string     $type   The type of request... GET, POST, PUT, DELETE
     * @param string     $path   The shop API path... /api/xxxx/xxxx
     * @param array|null $params Optional parameters to send with the request
     *
     * @return object An object of the Guzzle response, and JSON-decoded body
     * @throws \Exception
     */
    public function rest(string $type, string $path, array $params = null)
    {
        if ($this->shop === null) {
            // Shop is required
            throw new Exception('Shop domain missing for API calls');
        }

        if ($this->private && ($this->apiKey === null || $this->apiSecret === null)) {
            // Key and secret are required for private API calls
            throw new Exception('API key and secret required for private shop REST calls');
        }

        // Build the request parameters for Guzzle
        $guzzleParams = [];
        $guzzleParamsType = strtoupper($type) === 'GET' ? 'query' : 'json';
        $guzzleParams[$guzzleParamsType] = $params;

        if ($this->private) {
            $guzzleParams[$guzzleParamsType] = array_merge($params, ['key' => $this->apiKey]);
            ksort($guzzleParams[$guzzleParamsType]);
            $guzzleParams[$guzzleParamsType]['hmac'] = base64_encode(hash_hmac('sha256', json_encode($guzzleParams[$guzzleParamsType]), $this->apiSecret, true));
        }

        // Create the request, pass the access token and optional parameters
        $uri = (! preg_match('/^https?:\/\//', $this->shop) ? "https://" : "").rtrim($this->shop, '/').'/'.ltrim($path, '/');

        $response = $this->client->request($type, $uri, $guzzleParams);

        // Return Guzzle response and JSON-decoded body
        return (object) [
            'response' => $response,
            'body'     => $this->jsonDecode($response->getBody()),
        ];
    }

    /**
     * Decodes the JSON body.
     *
     * @param string $json The JSON body
     *
     * @return object The decoded JSON
     */
    protected function jsonDecode($json)
    {
        // From firebase/php-jwt
        if (! (defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
            /**
             * In PHP >=5.4.0, json_decode() accepts an options parameter, that allows you
             * to specify that large ints (like Steam Transaction IDs) should be treated as
             * strings, rather than the PHP default behaviour of converting them to floats.
             */
            $obj = json_decode($json, false, 512, JSON_BIGINT_AS_STRING);
        } else {
            // @codeCoverageIgnoreStart
            /**
             * Not all servers will support that, however, so for older versions we must
             * manually detect large ints in the JSON string and quote them (thus converting
             * them to strings) before decoding, hence the preg_replace() call.
             * Currently not sure how to test this so I ignored it for now.
             */
            $maxIntLength = strlen((string) PHP_INT_MAX) - 1;
            $jsonWithoutBigints = preg_replace('/:\s*(-?\d{'.$maxIntLength.',})/', ': "$1"', $json);
            $obj = json_decode($jsonWithoutBigints);
            // @codeCoverageIgnoreEnd
        }

        return $obj;
    }
}