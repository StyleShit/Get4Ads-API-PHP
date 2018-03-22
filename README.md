# Get4Ads-API-PHP
A PHP client for get4ads.com API

Installation
-
* Clone this repository
* Copy the `Get4AdsAPI.php` file into your project
* Include it in your php file

Usage
-

Here are some examples of the actions can be done using the API client:

```PHP
include 'Get4AdsAPI.php';

$apiToken = 'MY_API_TOKEN'; // required for authenticated endpoints
$version = '1';             // optional - default: 1
$testingMode = false;       // optional - default: false

$api = new Get4AdsAPI( $apiToken, $version, $testingMode );


// get all locations
$locations = $api->locations();

// search for location
$location = $api->findLocation( 'denver' );


// get all industries
$industries = $api->industries();

// search for industry
$industrys = $api->findIndustry( 'locksmith' );


// create new text lead
$data = [
    'industry-id' => 10, 
    'location-id' => 15, 
    'customer-name' => 'StyleShit',
    'customer-phone' => '111-333-7777',
    'customer-email' => '1337@h4x0r.com',
];

$response = $api->newTextLead( $data );
```

*NOTE:* All responses are in JSON format and parsed as a PHP object, according to the [API Documentation](https://get4ads.com/docs/v1) :

```PHP
// print all industries
$industries = $api->industries();

foreach( $industries->data as $industry )
{
    echo $industry->name.'<br />';
}
```