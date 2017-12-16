<?php

namespace Gtd\Middlewares;

class CheckAuth {
	
	public function __invoke($request, $response, $next) {
		$session = new \SlimSession\Helper();
		if (!$session->exists('userid') || !$session->exists('username') || !$session->exists('auth')) {
			return $response->withJson(['success' => false, 'message' => 'Low access level']);
		}
		$response = $next($request, $response);
		return $response;
	}
	
}