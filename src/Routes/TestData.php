<?php

namespace Gtd\Routes;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Gtd\Propel\User;
use \Gtd\Propel\TaskList;

class TestData {
	
	public static function init($app) {
		
		$app->get('/testdata/install', function(Request $request, Response $response, $args) {
			
			$user = new User();
			$user->setName('user');
			$user->setPass('123');
			$user->save();
			
			$homeList = new TaskList();
			$homeList->setUserId($user->getId());
			$homeList->setTitle('Дом');
			$homeList->save();
			
			$workList = new TaskList();
			$workList->setUserId($user->getId());
			$workList->setTitle('Работа');
			$workList->save();
			
			return $response->withJson(['success' => true]);
			
		});
		
	}
	
}