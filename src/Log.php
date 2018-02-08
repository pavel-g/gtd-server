<?php

namespace Gtd;

use Monolog\Logger as Monolog;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class Log
{
	
	public static $logger = null;
	
	public static function getLogger()
	{
		if (!self::$logger) {
			$formatter = new LineFormatter();
			// $formatter->allowInlineLineBreaks(false);
			// $formatter->includeStacktraces(true);
			$log = new Monolog('gtd');
			$stream = new StreamHandler(__DIR__ . '/../log', Monolog::DEBUG);
			$stream->setFormatter($formatter);
			$log->pushHandler($stream);
			self::$logger = $log;
		}
		return self::$logger;
	}
	
	public static function debug($msg, $args = null)
	{
		$log = self::getLogger();
		if ($args !== null) {
			return $log->debug($msg, $args);
		} else {
			return $log->debug($msg);
		}
	}
	
}