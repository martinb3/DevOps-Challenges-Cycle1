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



class Challenge3Command extends Command {

    protected function configure() {
        $this->setName('challenge3')
             ->setDescription('a script that prints a list of all of the DNS domains on an account. Let the user select a domain from the list and add an "A" record to that domain by entering an IP Address TTL, and requested "A" record text.')
             ->setHelp(<<<EOT
The <info>challenge3</info> command prints a list of all of the DNS domains on an account. Let the user select a domain from the list and add an "A" record to that domain by entering an IP Address TTL, and requested "A" record text
EOT
               );
    }


    protected function execute(InputInterface $input, OutputInterface $output) {

	$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
	    'username' => API_USERNAME,
	    'apiKey'   => API_KEY,
	));
	$output->writeln("Connecting to DNS service.");

        $dns = $client->dnsService(null, 'IAD');
	$dialog = $this->getHelperSet()->get('dialog');

	
	$domains = $dns->domainList();
	$domainpickerlist = array();
	foreach($domains as $d) {
	    array_push($domainpickerlist, $d->name);
	}

	if(count($domainpickerlist) <= 0) {
	    $output->error("No domains found");
	    return;
	}
	

	$output->writeln("The following domains were found:");
	foreach($domainpickerlist as $d) { $output->writeln(" <info>$d</info>"); };
	$output->writeln('');

	$name = $dialog->ask(
		$output,
		'<question>What domain do you want to operate on?</question> (autocomplete)  ',
		'',
		$domainpickerlist
	);

	$output->writeln('');
	$output->writeln("You selected $name");


	foreach($domains as $d) {
	    if($d->name == $name) {
		$domain = $d;
	    }
	}
	if(!isset($domain)) {
	    $output->writeln("<error>$name</error> was not a valid choice, exiting");
	    return;
	}

	$ipa = $dialog->ask($output, '<question>Please enter a valid IP address:</question> ');
	$ttl = $dialog->ask($output, '<question>Please enter a valid TTL:</question> ');
	$val = $dialog->ask($output, '<question>Please enter a valid text value:</question> ');

	$hostrecord = $domain->record(array(
		'name' => $val,
		'type' => 'A',
		'ttl'  => $ttl,
		'data' => $ipa,
	));
	$hostrecord->create();

	$output->writeln("<info>$val $ttl $ipa</info> created successfully");
    }

}
