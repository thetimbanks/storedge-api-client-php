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
            throw new Exception("\nSomething went wrong with API request. Error: \n" . $e->getMessage(), 500, $e);
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
            throw new Exception("\nSomething went wrong with API request. Error: \n" . $e->getMessage(), 500, $e);
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
            throw new Exception("\nSomething went wrong with API request. Error: \n" . $e->getMessage(), 500, $e);
        }
    }

    /**
     * Basic DELETE request wrapper that converts response to JSON
     *
     * @param  String $path         API endpoint path (i.e. "query")
     * @throws RedNovaLabs\Storedge\Exception
     * @return Object $response     Object containing information from request
     */
    protected function delete($path, $data)
    {
        try {
            // Sanitize path (Guzzle is pretty particular about this)
            $path = ltrim($path, './');

            // Do a DELETE request
            if ($data == null)
              $response = $this->getClient()->request('DELETE', $path);
            else
              $response = $this->getClient()->request('DELETE', $path, [
                  'json' => $data
              ]);

            // Try to convert the response to JSON
            return json_decode($response->getBody());

        } catch (BaseException $e) {
          throw new Exception("\nSomething went wrong with API request. Error: \n" . $e->getMessage(), 500, $e);
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
            throw new Exception("\nSomething went wrong with API request. Error: \n" . $e->getMessage(), 500, $e);
        }
    }

    protected function getQuery(array $options)
    {
        $query = '';
        $first = true;
        foreach ($options as $option => $value) {
          if ($first) {
            $query = $query . '?';
            $first = false;
          } else {
            $query = $query .'&';
          }

          $query = $query . $option . '=' . $value;
        }

        return $query;
    }

    //Leads
    public function getLeads($facility_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      try {
        return $this->get($base_url . $facility_uuid . '/leads' . $query);
      } catch (BaseException $e) {
        echo $e->getMessage();
      }
    }

    public function createLead($facility_uuid, array $data)
    {
      try {
        return $this->post($base_url . $facility_uuid . '/leads', $data);
      } catch (BaseException $e) {
        echo $e->getMessage();
      }
    }

    public function deleteLead($facility_uuid, $lead_uuid, $params)
    {
      try {
        return $this->delete($base_url . $facility_uuid . '/leads/' . $lead_uuid, $params);
      } catch (BaseException $e) {
        echo $e->getMessage();
      }

    }

    //Tenants
    public function updateTenantPATCH($facility_uuid, $tenant_uuid, array $data)
    {
      try {
        return $this->patch($base_url . $facility_uuid . '/tenants/' . $tenant_uuid, $data);
      } catch (BaseException $e) {
        echo $e->getMessage();
      }

    }

    public function updateTenantPUT($facility_uuid, $tenant_uuid, array $data)
    {
      try {
        return $this->put($base_url . $facility_uuid . '/tenants/' . $tenant_uuid, $data);
      } catch (BaseException $e) {
        echo $e->getMessage();
      }
    }

    public function signUpTenant($facility_uuid, $tenant_uuid, array $data)
    {
      try {
        return $this->post($base_url . $facility_uuid . '/tenants/' . $tenant_uuid . '/sign_up', $data);
      } catch (BaseException $e) {
        echo $e->getMessage();
      }
    }

    public function changeTenantPassword($facility_uuid, $tenant_uuid, array $data)
    {
      try {
        return $this->put($base_url . $facility_uuid . '/tenants/' . $tenant_uuid . '/change_password', $data);
      } catch (BaseException $e) {
        echo $e->getMessage();
      }
    }

    //Unit groups
    public function getUnitGroups($facility_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      try {
        return $this->get($base_url . $facility_uuid . '/unit_groups' . $query);
      } catch (BaseException $e) {
        echo $e->getMessage();
      }
    }

    public function getSpecificUnitGroup($facility_uuid, $unit_group_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      try {
        return $this->get($base_url . $facility_uuid . '/unit_groups/' . $unit_group_uuid . $query);
      } catch (BaseException $e) {
        echo $e->getMessage();
      }
    }

    public function getUnitGroupUnits($facility_uuid, $unit_group_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      try {
        return $this->get($base_url . $facility_uuid . '/unit_groups/' . $unit_group_uuid . '/units' . $query);
      } catch (BaseException $e) {
        echo $e->getMessage();
      }
    }

    //Units
    public function getUnits($facility_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      try {
        return $this->get($base_url . $facility_uuid . '/units' . $query);
      } catch (BaseException $e) {
        echo $e->getMessage();
      }
    }

    public function getAvailableUnits($facility_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      try {
        return $this->get($base_url . $facility_uuid . '/units/available' . $query);
      } catch (BaseException $e) {
        echo $e->getMessage();
      }
    }

    public function getSpecificUnit($facility_uuid, $unit_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      try {
        return $this->get($base_url . $facility_uuid . '/units/' . $unit_uuid . $query);
      } catch (BaseException $e) {
        echo $e->getMessage();
      }
    }

}
