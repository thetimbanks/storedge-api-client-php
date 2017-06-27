<?php
  require('vendor/autoload.php');

  date_default_timezone_set('America/Chicago');

  use RedNovaLabs\Storedge\SDK;

  // Define base url, key, and secret
  $base_url = 'https://api.storedgefms.com/v1/';
  $api_key  = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
  $api_secret = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

  // Set sample uuids
  $facility_uuid = '9df67dc0-293c-0135-3eed-10ddb1b10572';
  $unit_uuid = '9e017df0-293c-0135-3eed-10ddb1b10572';
  $tenant_uuid = 'b0948532-f8e6-46ce-b587-5d0218478506';
  $lead_uuid = '16a4a566-c3da-42d0-a73e-fbedc4dfdb33';

  //Create SDK client
  $client = new SDK($base_url, $api_key, $api_secret);

  // Sample requests
  // GET requests
  // Options array to append to url
  // You can add parameters to this too! ("start_date" => "1998-09-09" etc.)
  $options = array(
    "per_page" => "2",
    "page" => "2",
    "fields[unit]" => "name,unit_type",
    "fields[unit_type]" => "name",
  );

  $units = $client->getUnits($facility_uuid, $options);
  // You do not need to have options
  $availableUnits = $client->getAvailableUnits($facility_uuid, null);
  $specificUnit = $client->getSpecificUnit($facility_uuid, $unit_uuid, $options);

  // array of data to send in body of request
  $tenant = json_decode(
  '{
    "tenant": {
      "password": "supersecretpassword",
      "username": "awesome_o_5000"
    },
    "fields": {
      "tenant": "first_name,phone_numbers"
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
    },
    "fields": {
      "tenant": "first_name"
    }
  }', true);

  // PUT request
  $updatedTenant = $client->changeTenantPassword($facility_uuid, $tenant_uuid, $tenant);
  $tenant = json_decode(
  '{
    "tenant": {
      "first_name": "Kelvin",
      "last_name": "Benjamin",
      "phone_numbers_attributes": [
        {
          "number": "5656663476"
        }
      ],
      "mailing_address_attributes": {
        "address1": "Ikea St",
        "city": "Ikealand",
        "state": "KS"
      }
    },
    "fields": {
      "tenant": "last_name"
    }
  }', true);
  $updatedTenant = $client->updateTenantPATCH($facility_uuid, $tenant_uuid, $tenant);

  //Should work when given a valid close reason id
  $params = json_decode(
  '{
    "lead": {
      "close_reason_id": "1589a50b-f300-493e-ba95-70416baf216c",
      "notes_attributes": [
        {
          "note": "A++ would rent again!"
        }
      ]
    }
  }', true);

  $deletedLead = $client->deleteLead($facility_uuid, $lead_uuid, $params);

  die("\nDone!\n");
?>
