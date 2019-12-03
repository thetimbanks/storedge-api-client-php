# Storedge PHP SDK
Guzzle client wrapper for Storedge API

## Installation
*  This is a composer package, so [download composer](https://getcomposer.org/download/)
*  Add the Github URL to the `repositories` section of your composer.json file:

```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/thetimbanks/storedge-api-client-php"
    }
]
```

*  Set minimum stability in composer.json to `dev`
*  Run command: `composer require thetimbanks/storedge-sdk-php=master`

## API Documentation

### Building an SDK Client

```
<?php
require('vendor/autoload.php');
use RedNovaLabs\Storedge\SDK;

$base_url = 'https://api.storedgefms.com/v1/';
$api_key  = '[INSERT]';
$api_secret = '[INSERT]';
$client = new SDK($base_url, $api_key, $api_secret);

```

### Sending Requests
```
<?php
// ... continued

// Create any needed uuids
$facility_uuid = '[INSERT]';

// Send the request

// GET request
$units = $client->getUnits($facility_uuid);

// POST request
// array of data to send in body of request
$tenant = json_decode(
'{
  "tenant": {
    "password": "supersecretpassword",
    "username": "awesome_o_5000"
  }
}', true);

$newTenant = $client->signUpTenant($facility_uuid, $tenant_uuid, $tenant);

```
* See samples.php for more information
