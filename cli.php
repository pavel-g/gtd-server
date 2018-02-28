<?php

require(__DIR__ . '/vendor/autoload.php');

use GetOpt\GetOpt;
use GetOpt\Option;
use GetOpt\Command;
use GetOpt\ArgumentException;
use GetOpt\ArgumentException\Missing;
use \Gtd\Cli\Install;

// http://getopt-php.github.io/getopt-php/example.html

echo "Welcome to cli tool of Taskflower" . PHP_EOL;

$getopt = new GetOpt();

$getopt->addCommand(\GetOpt\Command::create('install', function () {
	$install = new Install();
	$install->install();
})->setDescription('Install taskflower'));

// process arguments and catch user errors
try {
	try {
		$getopt->process();
	} catch (Missing $exception) {
		// catch missing exceptions if help is requested
		if (!$getopt->getOption('help')) {
			throw $exception;
		}
	}
} catch (ArgumentException $exception) {
	file_put_contents('php://stderr', $exception->getMessage() . PHP_EOL);
	echo PHP_EOL . $getopt->getHelpText();
	exit;
}

// show help and quit
$command = $getopt->getCommand();

if (!$command) {
	exit;
}

// call the requested command
call_user_func($command->getHandler(), $getopt);