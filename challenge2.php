<?php

require_once 'bootstrap.php';

use OpenCloud\Rackspace;
use OpenCloud\Compute\Constants\ServerState;
use OpenCloud\Compute\Constants\Network;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressHelper;



class Challenge2Command extends Command {

    protected function configure() {
        $this->setName('challenge2')
             ->setDescription('a script that builds an arbitrary number of 512MB Cloud Servers, injects an ssh public key, and returns the root password and IP address for the server')
             ->setDefinition(array(
                new InputArgument('server_name_prefix', InputArgument::REQUIRED, 'server name prefix', null),
                new InputArgument('server_count', InputArgument::REQUIRED, 'server count', null),
                new InputArgument('public_key', InputArgument::REQUIRED, 'public key to inject', null),
		))
             ->setHelp(<<<EOT
The <info>challenge2</info> command builds an arbitrary number of 512MB Cloud Server, injects a public key for ssh, and returns the root password and IP address for the server
EOT
               );
    }


    protected function execute(InputInterface $input, OutputInterface $output) {

        $serverNamePrefix = $input->getArgument('server_name_prefix');
        $serverCount = $input->getArgument('server_count');
	$publicKey = $input->getArgument('public_key');

	if(!is_numeric($serverCount) || $serverCount <= 0 || $serverCount > 3) {
	    $output->writeln("<error>Cannot build $serverCount servers (only 1 to 3 servers allowed), exiting.");
	    exit(1);
	}

	if(!file_exists($publicKey)) {
	    $output->writeln("<error>File $publicKey does not exist, exiting.");
	    exit(1);
	}

	$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
	    'username' => API_USERNAME,
	    'apiKey'   => API_KEY,
	));
	$output->writeln("Connecting to compute service.");

        $compute = $client->computeService('cloudServersOpenStack', 'SYD');

	$keypairs = $compute->listKeypairs();
	$pair = null;
	while($keypair = $keypairs->next()) {
            if (strpos($keypair->getName(), 'devops-challenge-cycle1') !== false) {
                $pair = $keypair;
                break;
            }
	}

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

	$allservers = array();

	for($i = 1; $i <= $serverCount; $i++) {

		$serverName = "$serverNamePrefix$i";
		$server = $compute->server();
		array_push($allservers, $server);
		$progressarr[$serverName]=0;


		$output->writeln("Creating a server <info>$serverName</info> per your request.");
		$server->addFile('/root/.ssh/authorized_keys', file_get_contents($publicKey));
		$response = $server->create(array(
			'name'     => $serverName,
			'image'    => $ubuntu,
			'flavor'   => $twoGbFlavor,
			'networks' => array(
			    $compute->network(Network::RAX_PUBLIC),
			    $compute->network(Network::RAX_PRIVATE)
			),
		));

		$responseBody = (string) $response->getBody();
		$statusCode   = $response->getStatusCode();
		$headers      = $response->getHeaderLines();

		if(!$response->isSuccessful()) {
			$output->writeln("Something went wrong creating <info>$serverName</info>...<error>exiting</error>.");
			$output->writeln(sprintf("Status: %s\nBody: %s\nHeaders: %s", $statusCode, json_pretty_print($responseBody), implode(', ', $headers)));
			return;
		}

    	}

	$output->writeln("Waiting for all servers to become active to get all network addresses assigned to it.");

	foreach($allservers as $server) {
		$progress = $this->getHelperSet()->get('progress');
		$progress->setFormat(ProgressHelper::FORMAT_VERBOSE);

		$serverName = $server->name;
		$output->writeln("Waiting for <info>$serverName</info> to become active...");
		$progress->start($output, 100);


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
	

		$progress->start($output, 100);
	        $server->waitFor(ServerState::ACTIVE, 600, $callback);
                $progress->setCurrent(100);
	        $progress->finish();
		$output->writeln("<info>$serverName</info> has been created successfully.");
	}

	foreach($allservers as $server) {

	    $serverName = $server->name;
	    $adminPass = $server->adminPass;

            $output->writeln("Creation of server <info>$serverName</info> was a <info>success</info>.");
	    $output->writeln("Root password is: <info>$adminPass</info>");

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

	}

	$output->writeln("All servers have been created to allow access with public <info>$publicKey</info>.");
}

}
