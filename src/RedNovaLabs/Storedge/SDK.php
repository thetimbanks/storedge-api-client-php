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
      // Sanitize path (Guzzle is pretty particular about this)
      $path = ltrim($path, './');

      // Do a POST request
      $response = $this->getClient()->request('POST', $path, [
          'json' => $data,
          'http_errors' => false
      ]);

      // Try to convert the response to JSON
      return json_decode($response->getBody());
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
      // Sanitize path (Guzzle is pretty particular about this)
      $path = ltrim($path, './');

      // Do a PATCH request
      $response = $this->getClient()->request('PATCH', $path, [
          'json' => $data,
          'http_errors' => false
      ]);

      // Try to convert the response to JSON
      return json_decode($response->getBody());
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
      // Sanitize path (Guzzle is pretty particular about this)
      $path = ltrim($path, './');

      // Do a PUT request
      $response = $this->getClient()->request('PUT', $path, [
          'json' => $data,
          'http_errors' => false
      ]);

      // Try to convert the response to JSON
      return json_decode($response->getBody());
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
      // Sanitize path (Guzzle is pretty particular about this)
      $path = ltrim($path, './');

      // Do a DELETE request
      if ($data == null)
        $response = $this->getClient()->request('DELETE', $path);
      else
        $response = $this->getClient()->request('DELETE', $path, [
            'json' => $data,
            'http_errors' => false
        ]);

      // Try to convert the response to JSON
      return json_decode($response->getBody());
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
      // Sanitize path (Guzzle is pretty particular about this)
      $path = ltrim($path, './');

      // Do a GET request
      $response = $this->getClient()->request('GET', $path);

      // Try to convert the response to JSON
      return json_decode($response->getBody());
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

    //Facility
    public function getFacility($facility_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      return $this->get($this->base_url . $facility_uuid . '/info' . $query);
    }

    //Leads
    public function getLeads($facility_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      return $this->get($this->base_url . $facility_uuid . '/leads' . $query);
    }

    public function createLead($facility_uuid, array $data)
    {
      return $this->post($this->base_url . $facility_uuid . '/leads', $data);
    }

    public function deleteLead($facility_uuid, $lead_uuid, $params)
    {
      return $this->delete($this->base_url . $facility_uuid . '/leads/' . $lead_uuid, $params);
    }

    //Tenants
    public function updateTenantPATCH($facility_uuid, $tenant_uuid, array $data)
    {
      return $this->patch($this->base_url . $facility_uuid . '/tenants/' . $tenant_uuid, $data);
    }

    public function updateTenantPUT($facility_uuid, $tenant_uuid, array $data)
    {
      return $this->put($this->base_url . $facility_uuid . '/tenants/' . $tenant_uuid, $data);
    }

    public function signUpTenant($facility_uuid, $tenant_uuid, array $data)
    {
      return $this->post($this->base_url . $facility_uuid . '/tenants/' . $tenant_uuid . '/sign_up', $data);
    }

    public function changeTenantPassword($facility_uuid, $tenant_uuid, array $data)
    {
      return $this->put($this->base_url . $facility_uuid . '/tenants/' . $tenant_uuid . '/change_password', $data);
    }

    //Unit groups
    public function getUnitGroups($facility_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      return $this->get($this->base_url . $facility_uuid . '/unit_groups' . $query);
    }

    public function getSpecificUnitGroup($facility_uuid, $unit_group_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      return $this->get($this->base_url . $facility_uuid . '/unit_groups/' . $unit_group_uuid . $query);
    }

    public function getUnitGroupUnits($facility_uuid, $unit_group_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      return $this->get($this->base_url . $facility_uuid . '/unit_groups/' . $unit_group_uuid . '/units' . $query);
    }

    //Units
    public function getUnits($facility_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      return $this->get($this->base_url . $facility_uuid . '/units' . $query);
    }

    public function getAvailableUnits($facility_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      return $this->get($this->base_url . $facility_uuid . '/units/available' . $query);
    }

    public function getSpecificUnit($facility_uuid, $unit_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      return $this->get($this->base_url . $facility_uuid . '/units/' . $unit_uuid . $query);
    }

    public function getDiscountPlan($facility_uuid, $discount_plan_uuid, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      return $this->get($this->base_url . $facility_uuid . '/discount_plans/' . $discount_plan_uuid . $query);
    }

    public function signInTenant($facility_uuid, array $data, $options)
    {
      $query = '';
      if ($options != null)
        $query = $this->getQuery($options);

      return $this->post($this->base_url . $facility_uuid . '/tenants/sign_in' . $query, $data);
    }

    public function processMoveIn($facility_uuid, array $data)
    {
      return $this->post($this->base_url . $facility_uuid . '/move_ins/process_move_in', $data);
    }

    public function reviewCost($url_override, $facility_uuid, $unit_uuid, array $data)
    {
      $reviewCostUrl = isset($url_override) ? $url_override : $this->base_url . $facility_uuid . '/move_ins/review_cost';
      $data['move_in']['should_generate_documents'] = false;
      return $this->post($reviewCostUrl, $data);
    }

    public function getInsurancePolicies($facility_uuid)
    {
      return $this->get($this->base_url . $facility_uuid . '/invoiceable_items/insurance');
    }

    public function getTenantPortalSettings($facility_uuid)
    {
      return $this->get($this->base_url . $facility_uuid . '/tenant_portal_settings');
    }

    public function getFacilityServices($facility_uuid)
    {
      return $this->get($this->base_url . $facility_uuid . '/invoiceable_items/services');
    }

    public function getFacilityRentalCenterInvoiceableItems($facility_uuid)
    {
      return $this->get($this->base_url . $facility_uuid . '/invoiceable_items/rental_center');
    }
}
