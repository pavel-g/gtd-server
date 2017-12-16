<?php

namespace Gtd\Routes;

use \Gtd\Propel\UserQuery;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Auth {
	
	public static function init($app) {
		
		$app->group('/auth', function() {
			
			$this->post('/login', function(Request $request, Response $response, $args) {
				$session = $this->session;
				if ($session->exists('userid') && $session->exists('username') && $session->exists('auth')) {
					return $response->withJson(['success' => true]);
				}
				$parsedBody = $request->getParsedBody();
				$username = $parsedBody['username'];
				$password = $parsedBody['password'];
				$user = UserQuery::create()->filterByName($username)->filterByPass($password)->findOne();
				if (!empty($user)) {
					$session->set('userid', $user->getId());
					$session->set('username', $user->getName());
					$session->set('auth', true);
					return $response->withJson(['success' => true]);
				}
				return $response->withJson(['success' => false]);
			});
			
			$this->get('/login', function(Request $request, Response $response, $args) {
				$session = $this->session;
				if ($session->exists('userid') && $session->exists('username') && $session->exists('auth')) {
					return $response->withJson(['success' => true]);
				}
				return $response->withJson(['success' => false]);
			});
			
			$logout = function(Request $request, Response $response, $args) {
				$this->session->destroy();
				return $response->withJson(['success' => true]);
			};
			
			$this->get('/logout', $logout);
			$this->post('/logout', $logout);
			
		});
		
	}
	
}