<?php

namespace Gtd;

class ErrorHandler {
	
	public static function init($app) {
		$c = $app->getContainer();
		$c['errorHandler'] = function($c) {
			return function ($request, $response, $exception) use ($c) {
				return $c['response']->withStatus(500)
				                     ->withHeader('Content-Type', 'application/json')
				                     ->write(json_encode([
				                         'success' => false,
				                         'message' => $exception->getMessage(),
				                         'trace' => $exception->getTrace()
				                     ]));
			};
		};
	}
	
}