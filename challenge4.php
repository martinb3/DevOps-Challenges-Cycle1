<?php

require 'vendor/autoload.php';
require 'secrets.php';

use OpenCloud\Rackspace;

$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
    'username' => API_USERNAME,
    'apiKey'   => API_KEY,
));

$compute = $client->computeService('cloudServersOpenStack', 'ORD');

