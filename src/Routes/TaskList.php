<?php

namespace Gtd\Routes;

use \Gtd\CheckAuth;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Gtd\Propel\TaskListQuery;
use \Gtd\Propel\TaskList as TaskListRecord;
use \Gtd\Util;

class TaskList {
	
	public static function init($app) {
		
		$app->group('/list', function() {
			
			$this->get('/all', function(Request $request, Response $response, $args) {
				$userId = $this->session->get('userid');
				$data = TaskListQuery::create()->filterByUserId($userId)->filterByRemoved(null)->find();
				$data = Util::storeKeysCamelCaseToUnderscore($data->toArray());
				return $response->withJson(['success' => true, 'data' => $data]);
			});
			
			$this->post('/create', function(Request $request, Response $response, $args) {
				$userId = $this->session->get('userid');
				$body = $request->getParsedBody();
				if (!array_key_exists('title', $body) || !is_string($body['title'])) {
					return $response->withJson(['success' => false, 'message' => 'Unexpected title value']);
				}
				$title = $body['title'];
				$list = new TaskListRecord();
				$list->setTitle($title);
				$list->setUserId($userId);
				$list->save();
				$data = Util::recordKeysCamelCaseToUnderscore($list->toArray());
				return $response->withJson(['success' => true, 'data' => $data]);
			});
			
			$this->post('/remove', function(Request $request, Response $response, $args) {
				$userId = $this->session->get('userid');
				$body = $request->getParsedBody();
				if (!array_key_exists('id', $body) || !is_numeric($body['id'])) {
					return $response->withJson(['success' => false, 'message' => 'Unexpected id value']);
				}
				$id = (integer) $body['id'];
				$list = TaskListQuery::create()->findPk($id);
				if (empty($list)) {
					return $response->withJson(['success' => false, 'message' => 'Tasks list not exists']);
				}
				if ($list->getUserId() !== $userId) {
					return $response->withJson(['success' => false, 'message' => 'Low access level']);
				}
				$list->setRemoved(new \DateTime('now'));
				$list->save();
				return $response->withJson(['success' => true]);
			});
			
		})->add(new \Gtd\Middlewares\CheckAuth());
		
	}
	
}