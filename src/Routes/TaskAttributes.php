<?php

namespace Gtd\Routes;


use Slim\App;
use Gtd\Controllers\TaskAttributes as TaskAttributesController;

class TaskAttributes
{
	
	public static function init(App $app)
	{
		$container = $app->getContainer();
		
		$container['TaskAttributes'] = function ($container) {
			return new TaskAttributesController($container);
		};
		
		$app->group('/attributes', function () {
			$this->get('/get', TaskAttributesController::class . ':getAction');
			$this->post('/save', TaskAttributesController::class . ':saveAction');
			$this->post('/delete', TaskAttributesController::class . ':deleteAction');
			$this->get('/types', TaskAttributesController::class . ':typesAction');
		});
	}
	
}