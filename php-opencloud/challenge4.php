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



class Challenge4Command extends Command {

    protected function configure() {
        $this->setName('challenge4')
             ->setDescription('a script that creates a Cloud Files Container. If the container already exists, exit and let the user know. The script should also upload a directory from the local filesystem to the new container, and enable CDN for the new container. The script must return the CDN URL.')
             ->setDefinition(array(
                new InputArgument('container_name', InputArgument::REQUIRED, 'container name', null),
                new InputArgument('upload_directory', InputArgument::REQUIRED, 'upload_directory', null),
                ))
             ->setHelp(<<<EOT
The <info>challenge4</info> command creates a Cloud Files Container. If the container already exists, exit and let the user know. The script should also upload a directory from the local filesystem to the new container, and enable CDN for the new container. The script must returns the CDN URL
EOT
               );
    }


    protected function execute(InputInterface $input, OutputInterface $output) {

        $uploadDirectory = $input->getArgument('upload_directory');
        $containerName = $input->getArgument('container_name');


	if(!file_exists($uploadDirectory) || !is_dir($uploadDirectory)) {
		$output->writeln("Directory <info>$uploadDirectory</info> either <error>does not exist, or isn't a directory, exiting</error>");
		return;
	}

	$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
	    'username' => API_USERNAME,
	    'apiKey'   => API_KEY,
	));
	$output->writeln("Connecting to object store service.");

	$filesService = $client->objectStoreService('cloudFiles', 'IAD');

	$statusCode = 200;
	try {
		$testContainer = $filesService->getContainer($containerName);
	} catch (Exception $e) {
		$statusCode   = $e->getResponse()->getStatusCode();
	}

	if($statusCode != 404) {
		$output->writeln("Container <info>$containerName</info> already found, <error>won't overwrite, exiting</error>");
		return;
	}


	$container = $filesService->createContainer($containerName);

	$container->uploadDirectory($uploadDirectory);

	$output->writeln("<info>$containerName</info> created successfully from $uploadDirectory");

	$container->enableCdn();
	$cdn = $container->getCdn();

	$uri = $cdn->getCdnUri();
	$output->writeln("<info>$containerName</info> CDN URL is <info>$uri</info>");
    }

}
