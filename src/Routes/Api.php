<?php

namespace Gtd\Routes;

use \Gtd\CheckAuth;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Gtd\Propel\TaskListQuery;
use \Gtd\Propel\TaskList;
use \Gtd\Util;

class Api {
	
	public static function init($app) {
		
		$app->group('/api', function() {
			
			$this->get('/lists', function(Request $request, Response $response, $args) {
				$userId = $this->session->get('userid');
				$data = TaskListQuery::create()->findByUserId($userId);
				$data = Util::storeKeysCamelCaseToUnderscore($data->toArray());
				return $response->withJson(['success' => true, 'data' => $data]);
			});
			
			$this->post('/list/create', function(Request $request, Response $response, $args) {
				$userId = $this->session->get('userid');
				$body = $request->getParsedBody();
				if (!array_key_exists('title', $body) || !is_string($body['title'])) {
					return $response->withJson(['success' => false, 'message' => 'Unexpected title value']);
				}
				$title = $body['title'];
				$list = new TaskList();
				$list->setTitle($title);
				$list->setUserId($userId);
				$list->save();
				$data = Util::recordKeysCamelCaseToUnderscore($list->toArray());
				return $response->withJson(['success' => true, 'data' => $data]);
			});
			
			$this->get('/list/delete/{id:[0-9]+}', function(Request $request, Response $response, $args) {
				$userId = $this->session->get('userid');
				$id = (integer) $args['id'];
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