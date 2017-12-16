<?php

namespace Gtd\Routes;

use \Gtd\CheckAuth;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Api {
	
	public static function init($app) {
		
		$app->group('/api', function() {
			$this->get('/test', function(Request $request, Response $response, $args) {
				return $response->withJson(['success' => true, 'message' => 'SUCCESS']);
			});
		})->add(function(Request $request, Response $response, $next) {
			$session = $this->session;
			if (!$session->exists('userid') || !$session->exists('username') || !$session->exists('auth')) {
				return $response->withJson(['success' => false, 'message' => 'Need auth']);
			}
			$response = $next($request, $response);
			return $response;
		});
		
	}
	
}