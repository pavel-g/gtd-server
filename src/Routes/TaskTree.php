<?php

namespace Gtd\Routes;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Gtd\Middlewares\CheckAuth;
use \Gtd\Propel\TaskTree as TaskTreeRecord;
use \Gtd\Controllers\TaskTree as TaskTreeController;

class TaskTree {
	
	public static function init($app) {
		
		$container = $app->getContainer();
		
		$container['TaskTree'] = function($container) {
			return new TaskTreeController($container);
		};
		
		$app->group('/tree', function() {
			
			$this->get('/all', TaskTreeController::class . ':getAllAction');
			
			$this->post('/create', TaskTreeController::class . ':createTaskAction');
			
			$this->get('/full', TaskTreeController::class . ':getFullTreeAction');
			
		})->add(new CheckAuth());
		
	}
	
}