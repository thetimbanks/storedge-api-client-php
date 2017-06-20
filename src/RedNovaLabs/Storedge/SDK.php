<?php
namespace RedNovaLabs\Storedge;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Exception as BaseException;

class SDK {

    const DEFAULT_HTTP_TIMEOUT = 10.0;

    protected $client;
    protected $base_url;
    protected $api_key;
    protected $api_secret;
    protected $timeout;

    /**
     * Client constructor
     *
     * @param String $base_url     Base URL to API
     * @param String $api_key      Authorized API Access Key
     * @param String $api_secret   Authorized API Secert
     * @param Float $timeout       HTTP request timeout
     */
    public function __construct($base_url, $api_key, $api_secret, $timeout = self::DEFAULT_HTTP_TIMEOUT)
    {
        // Trailing slash is important on the base URL
        $this->base_url = rtrim($base_url, '/') . '/';
        $this->api_key  = $api_key;
        $this->api_secret = $api_secret;
        $this->timeout  = $timeout;
    }

    /**
     * Create reusable base Guzzle client object
     *
     * @return GuzzleHttp\Client
     */
    protected function makeClient()
    {
      //Generate OAuth1.0 handler
        $stack = HandlerStack::create();

        $middleware = new Oauth1([
          'consumer_key'    => $this->api_key,
          'consumer_secret' => $this->api_secret
        ]);
        $stack->push($middleware);

        return new Client([
            'base_uri' => $this->base_url,
            'timeout'  => $this->timeout,
            'handler' => $stack,
            'auth' => 'oauth'
        ]);
    }

    /**
     * Gets or creates the Guzzle client
     *
     * @return GuzzleHttp\Client
     */
    public function getClient()
    {
        if (empty($this->client)) {
            $this->client = $this->makeClient();
        }

        return $this->client;
    }

    /**
     * Basic POST request wrapper that converts response to JSON object
     *
     * @param  String $path         API endpoint path (i.e. "query")
     * @param  Array  $data         Array of request body data to pass as JSON
     * @throws RedNovaLabs\Storedge\Exception
     * @return Object $response     Object containing information from request
     */
    protected function post($path, array $data)
    {
        try {
            // Sanitize path (Guzzle is pretty particular about this)
            $path = ltrim($path, './');

            // Do a POST request
            $response = $this->getClient()->request('POST', $path, [
                'json' => $data
            ]);

            // Try to convert the response to JSON
            return json_decode($response->getBody());

        } catch (BaseException $e) {
            throw new Exception('Something went wrong with API request', 500, $e);
        }
    }

    /**
     * Basic PATCH request wrapper that converts response to JSON object
     *
     * @param  String $path         API endpoint path (i.e. "query")
     * @param  Array  $data         Array of request body data to pass as JSON
     * @throws RedNovaLabs\Storedge\Exception
     * @return Object $response     Object containing information from request
     */
    protected function patch($path, array $data)
    {
        try {
            // Sanitize path (Guzzle is pretty particular about this)
            $path = ltrim($path, './');

            // Do a PATCH request
            $response = $this->getClient()->request('PATCH', $path, [
                'json' => $data
            ]);

            // Try to convert the response to JSON
            return json_decode($response->getBody());

        } catch (BaseException $e) {
            throw new Exception('Something went wrong with API request', 500, $e);
        }
    }

    /**
     * Basic PUT request wrapper that converts response to JSON object
     *
     * @param  String $path         API endpoint path (i.e. "query")
     * @param  Array  $data         Array of request body data to pass as JSON
     * @throws RedNovaLabs\Storedge\Exception
     * @return Object $response     Object containing information from request
     */
    protected function put($path, array $data)
    {
        try {
            // Sanitize path (Guzzle is pretty particular about this)
            $path = ltrim($path, './');

            // Do a PUT request
            $response = $this->getClient()->request('PUT', $path, [
                'json' => $data
            ]);

            // Try to convert the response to JSON
            return json_decode($response->getBody());

        } catch (BaseException $e) {
            throw new Exception('Something went wrong with API request', 500, $e);
        }
    }

    /**
     * Basic DELETE request wrapper that converts response to JSON
     *
     * @param  String $path         API endpoint path (i.e. "query")
     * @throws RedNovaLabs\Storedge\Exception
     * @return Object $response     Object containing information from request
     */
    protected function delete($path)
    {
        try {
            // Sanitize path (Guzzle is pretty particular about this)
            $path = ltrim($path, './');

            // Do a DELETE request
            $response = $this->getClient()->request('DELETE', $path);

            // Try to convert the response to JSON
            return json_decode($response->getBody());

        } catch (BaseException $e) {
            throw new Exception('Something went wrong with API request', 500, $e);
        }
    }

    /**
     * Basic GET request wrapper that converts response to JSON
     *
     * @param  String $path         API endpoint path (i.e. "query")
     * @throws RedNovaLabs\Storedge\Exception
     * @return Object $response     Object containing information from request
     */
    protected function get($path)
    {
        try {
            // Sanitize path (Guzzle is pretty particular about this)
            $path = ltrim($path, './');

            // Do a GET request
            $response = $this->getClient()->request('GET', $path);

            // Try to convert the response to JSON
            return json_decode($response->getBody());

        } catch (BaseException $e) {
            throw new Exception('Something went wrong with API request', 500, $e);
        }
    }

    //Units
    public function getUnits($facility_uuid)
    {
      return $this->get($base_url . $facility_uuid . '/units');
    }

    public function getAvailableUnits($facility_uuid)
    {
      return $this->get($base_url . $facility_uuid . '/units/available');
    }

    public function getSpecificUnit($facility_uuid, $unit_uuid)
    {
      return $this->get($base_url . $facility_uuid . '/units/' . $unit_uuid);
    }

    //Tenants
    public function changeTenantPassword($facility_uuid, $tenant_uuid, array $data)
    {
      return $this->put($base_url . $facility_uuid . '/tenants/' . $tenant_uuid . '/change_password', $data);
    }

    public function signUpTenant($facility_uuid, $tenant_uuid, array $data)
    {
      return $this->post($base_url . $facility_uuid . '/tenants/' . $tenant_uuid . '/sign_up', $data);
    }

}
