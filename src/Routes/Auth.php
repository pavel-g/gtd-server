<?php

namespace Gtd\Routes;

use \Gtd\AuthHelper;

class Auth {
	
	public static function init($app) {
		
		$app->group('/auth', function() {
			
			$this->post('/check', function($request, $response, $args) {
				$parsedBody = $request->getParsedBody();
				$res = AuthHelper::check($parsedBody['username'], $parsedBody['password']);
				return $response->withJson(['success' => $res]);
			});
			
		});
		
	}
	
}