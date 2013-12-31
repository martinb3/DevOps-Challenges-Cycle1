<?php

header("Content-type: text/plain");

require 'vendor/autoload.php';
require 'secrets.php';

use OpenCloud\Rackspace;

$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
    'username' => API_USERNAME,
    'apiKey'   => API_KEY,
));

$compute = $client->computeService('cloudServersOpenStack', 'ORD');

$images = $compute->imageList();
while ($image = $images->next()) {
    if (strpos($image->name, 'Ubuntu') !== false) {
        $ubuntu = $image;
        break;
    }
}

$flavors = $compute->flavorList();
while ($flavor = $flavors->next()) {
    if (strpos($flavor->name, '512MB') !== false) {
        $twoGbFlavor = $flavor;
        break;
    }
}

use OpenCloud\Compute\Constants\Network;

$server = $compute->server();

try {
    $response = $server->create(array(
        'name'     => 'My lovely server',
        'image'    => $ubuntu,
        'flavor'   => $twoGbFlavor,
        'networks' => array(
            $compute->network(Network::RAX_PUBLIC),
            $compute->network(Network::RAX_PRIVATE)
        )
    ));

    $responseBody = (string) $response->getBody();
    $statusCode   = $response->getStatusCode();
    $headers      = $response->getHeaderLines();

    echo sprintf("Status: %s\nBody: %s\nHeaders: %s", $statusCode, $responseBody, implode(', ', $headers));


} catch (\Guzzle\Http\Exception\BadResponseException $e) {

    // No! Something failed. Let's find out:

    $responseBody = (string) $e->getResponse()->getBody();
    $statusCode   = $e->getResponse()->getStatusCode();
    $headers      = $e->getResponse()->getHeaderLines();

    echo sprintf("Status: %s\nBody: %s\nHeaders: %s", $statusCode, $responseBody, implode(', ', $headers));
}

