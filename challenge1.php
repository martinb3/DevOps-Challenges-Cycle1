<?php

require 'bootstrap.php';

use OpenCloud\Rackspace;
use OpenCloud\Compute\Constants\ServerState;
use OpenCloud\Compute\Constants\Network;

$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
    'username' => API_USERNAME,
    'apiKey'   => API_KEY,
));

$compute = $client->computeService('cloudServersOpenStack', 'IAD');

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

    if(!$response->isSuccessful()) {
        print "Something went wrong...\n";
        print sprintf("Status: %s\nBody: %s\nHeaders: %s", $statusCode, prettyPrint($responseBody), implode(', ', $headers));
    }

    $jsonResponse = json_decode($responseBody, true);
    $adminPass = isset($jsonResponse['server']['adminPass']) ? $jsonResponse['server']['adminPass'] : "unknown";

    print "Creation of server was a success.\n";
    print "Root password is: $adminPass\n";

    $callback = function($server) {
	static $pc = 0;
        if (!empty($server->error)) {
            print "Unable to poll this server to see it build\n";
            var_dump($server->error);
            exit;
        } else {
            if($pc != $server->progress) {
            echo sprintf(
            "Waiting on %s/%-12s %4s%%\n",
            $server->name(),
            $server->status(),
            isset($server->progress) ? $server->progress : 0
            );
	    }
	    $pc = $server->progress;
        }
    };

    $server->waitFor(ServerState::ACTIVE, 600, $callback);

    $addresses = (array)$server->addresses;

    print "IP addresses are:\n";
    foreach($addresses as $type => $addr) {
	// type is public or private
	foreach($addr as $ip) {
            $ipa = (array)$ip;
            $v = $ipa['addr'];
            print "\t$type: $v\n";
        }
    }

} catch (\Guzzle\Http\Exception\BadResponseException $e) {

    // No! Something failed. Let's find out:

    $responseBody = (string) $e->getResponse()->getBody();
    $statusCode   = $e->getResponse()->getStatusCode();
    $headers      = $e->getResponse()->getHeaderLines();

    echo sprintf("Status: %s\nBody: %s\nHeaders: %s", $statusCode, $responseBody, implode(', ', $headers));
}

print "\n";
