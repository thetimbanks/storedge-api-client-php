<?php
  require('vendor/autoload.php');

  date_default_timezone_set('America/Chicago');

  use RedNovaLabs\Storedge\Event;
  use RedNovaLabs\Storedge\Query;
  use RedNovaLabs\Storedge\SDK;

  // Define base url, key, and secret
  $base_url = 'https://api.storedgefms.com/v1/';
  $api_key  = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
  $api_secret = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

  // Set sample uuids
  $facility_uuid = '6b1b0fd0-293c-0135-3eed-10ddb1b10572';
  $tenant_uuid = '16863143-056f-4c7b-babd-1b0d7dab55a7';

  //Create SDK client
  $client = new SDK($base_url, $api_key, $api_secret);

  // Sample requests
  //GET requests
  $units = $client->getUnits($facility_uuid);
  $availableUnits = $client->getAvailableUnits($facility_uuid);

  // array of data to send in body of request
  $tenant = json_decode(
  '{
    "tenant": {
        "password": "supersecretpassword",
      "username": "awesome_o_5000"
    }
  }', true);

  // POST request
  $newTenant = $client->signUpTenant($facility_uuid, $tenant_uuid, $tenant);

  // array of data to send in body of request
  $tenant = json_decode(
  '{
    "tenant": {
         "current_password": "supersecretpassword",
       "new_password": "super_new_password"
    }
  }', true);

  // PUT request
  $updatedTenant = $client->changeTenantPassword($facility_uuid, $tenant_uuid, $tenant);

  die("Done!\n");
?>
