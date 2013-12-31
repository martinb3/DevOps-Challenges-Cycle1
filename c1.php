<?php

require 'secrets.php';
require 'vendor/autoload.php';

$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
    'username' => API_USERNAME,
    'apiKey'   => API_KEY,
));


