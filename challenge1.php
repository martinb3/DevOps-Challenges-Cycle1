<?php

require 'bootstrap.php';

use OpenCloud\Rackspace;
use OpenCloud\Compute\Constants\ServerState;
use OpenCloud\Compute\Constants\Network;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressHelper;



class Challenge1Command extends Command {

    protected function configure() {
        $this->setName('challenge1')
             ->setDescription('a script that builds a 512MB Cloud Server and returns the root password and IP address for the server')
             ->setDefinition(array(
                new InputArgument('server', InputArgument::REQUIRED, 'server name', null),
		))
             ->setHelp(<<<EOT
The <info>challenge1</info> command builds a 512MB Cloud Server and returns the root password and IP address for the server
EOT
               );
    }


    protected function execute(InputInterface $input, OutputInterface $output) {

        $serverName = $input->getArgument('server');
	if(!isset($serverName)) {
	    $output->writeln("<error>Missing argument server</error>, can't build a no name server!");
	    return;
	}

	$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
	    'username' => API_USERNAME,
	    'apiKey'   => API_KEY,
	));
	$output->writeln("Creating a server <info>$serverName</info> per your request.");

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
		'name'     => $serverName,
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
		$output->writeln("Something went wrong...");
		$output->writeln(sprintf("Status: %s\nBody: %s\nHeaders: %s", $statusCode, json_pretty_print($responseBody), implode(', ', $headers)));
		return;
	    }

	    $jsonResponse = json_decode($responseBody, true);
	    $adminPass = isset($jsonResponse['server']['adminPass']) ? $jsonResponse['server']['adminPass'] : "unknown";

	    $output->writeln("Creation of server <info>$serverName</info> was a <info>success</info>.");
	    $output->writeln("Root password is: <info>$adminPass</info>");


	    $progress = $this->getHelperSet()->get('progress');
	    $progress->setFormat(ProgressHelper::FORMAT_VERBOSE);
	    $callback = function($server) use ($output, $progress) {
		static $pc = 0;
		if (!empty($server->error)) {
		    $output->writeln("Unable to poll this server to see it build. You may need to delete it manually and retry.");
		    $output->writeln(var_export($server->error, true));
		    exit;
		} else {
		    if($pc != $server->progress) {
			    #$output->writeln(sprintf(
			    #"Waiting on %s/%-12s %4s%%",
			    #$server->name(),
			    #$server->status(),
			    #isset($server->progress) ? $server->progress : 0
			    #));
			    $progress->setCurrent($pc);
		    }
		    else { $progress->display(); }
		    $pc = $server->progress;
		}
	    };

	    $output->writeln("Waiting for server to become active to get all network addresses assigned to it.");

	    $progress->start($output, 100);
	    $server->waitFor(ServerState::ACTIVE, 600, $callback);
            $progress->setCurrent(100);
	    $progress->finish();

	    $addresses = (array)$server->addresses;

	    $output->writeln("IP addresses are:");
	    foreach($addresses as $type => $addr) {
		// type is public or private
		foreach($addr as $ip) {
		    $ipa = (array)$ip;
		    $v = $ipa['addr'];
		    $output->writeln("\t$type: <info>$v</info>");
		}
	    }

	} catch (\Guzzle\Http\Exception\BadResponseException $e) {

	    // No! Something failed. Let's find out:

	    $responseBody = (string) $e->getResponse()->getBody();
	    $statusCode   = $e->getResponse()->getStatusCode();
	    $headers      = $e->getResponse()->getHeaderLines();

	    $output->writeln(sprintf("Status: %s\nBody: %s\nHeaders: %s", $statusCode, $responseBody, implode(', ', $headers)));
	}

}

}
