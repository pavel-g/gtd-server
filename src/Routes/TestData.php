<?php

namespace Gtd\Routes;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Gtd\Propel\User;
use \Gtd\Propel\TaskList;
use \Gtd\Propel\TaskTree;
use \Propel\Runtime\Map\TableMap;

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
			
			$homeListId = $homeList->getId();
			
			self::createTasks($homeListId, [
				['title' => 'task1', 'path' => ''], // 1
				['title' => 'task1.1', 'path' => '1', 'parent_id' => 1], // 2
				['title' => 'task1.1.1', 'path' => '1/2', 'parent_id' => 2], // 3
				['title' => 'task2', 'path' => ''], // 4
				['title' => 'task2.1', 'path' => '4', 'parent_id' => 4], // 5
				['title' => 'task3', 'path' => ''], // 6
				['title' => 'task3.1', 'path' => '6', 'parent_id' => 6], // 7
				['title' => 'task3.2', 'path' => '6', 'parent_id' => 6], // 8
				['title' => 'task4', 'path' => ''] // 9
			]);
			
			return $response->withJson(['success' => true]);
			
		});
		
	}
	
	public static function createTask($listId, $params) {
		$task = new TaskTree();
		$task->setTitle($params['title']);
		$task->setListId($listId);
		$task->save();
		$task->setSmartParentId($params['parent_id']);
	}
	
	public static function createTasks($listId, $params) {
		for( $i = 0; $i < count($params); $i++ ) {
			self::createTask($listId, $params[$i]);
		}
	}
	
}