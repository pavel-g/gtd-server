<?php

require(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/generated-conf/config.php');

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

$getopt->addCommand(Command::create('test-data', function () {
	$testdata = new \Gtd\Cli\TestData();
	$testdata->install();
}));

$getopt->addCommand(Command::create('generate', function () {
	$generate = new \Gtd\Cli\Generate();
	$generate->install();
}));

$getopt->addCommand(Command::create('all', function() {
	$generate = new \Gtd\Cli\Generate();
	$generate->install();
	$install = new Install();
	$install->install();
	$testdata = new \Gtd\Cli\TestData();
	$testdata->install();
}));

// process arguments and catch user errors
try {
	$getopt->process();
} catch (ArgumentException $exception) {
	file_put_contents('php://stderr', $exception->getMessage() . PHP_EOL);
	exit;
}

// show help and quit
$command = $getopt->getCommand();

if (!$command) {
	exit;
}

// call the requested command
call_user_func($command->getHandler(), $getopt);