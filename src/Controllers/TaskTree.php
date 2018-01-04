<?php

namespace Gtd\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Container\ContainerInterface;
use \Gtd\Propel\TaskTree as TaskTreeRecord;
use \Gtd\BodyParser;
use \Gtd\Propel\TaskListQuery;

class TaskTree {
	
	protected $container;
	
	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}
	
	public function getAllAction(Request $request, Response $response, $args) {
		return $response->withJson(['success' => true, 'data' => null]);
	}
	
	public function createTaskAction(Request $request, Response $response, $args) {
		$body = $request->getParsedBody();
		$body = new BodyParser();
		$body->setBody($request->getParsedBody());
		$parentId = $body->getParam('parent_id', null);
		$listId = $body->getParam('list_id');
		if (!$this->checkListId($listId)) {
			return $response->withJson(['success' => false, 'message' => 'Low access level']);
		}
		$task = new TaskTreeRecord();
		$task->setListId($listId);
		$task->setTitle($body->getParam('title'));
		$task->setDescription($body->getParam('description'));
		$task->setCreated(new \DateTime('now'));
		$task->setDue($body->getParam('due'));
		if ($parentId === null) {
			$task->setPath('/');
		} else {
			// TODO: 2018-01-03 Set parent id
		}
		$task->save();
		return $response->withJson(['success' => true, 'data' => $task->toArray()]);
	}
	
	protected function getSession() {
		return $this->container['session'];
	}
	
	protected function getUserId() {
		$session = $this->getSession();
		return $session->get('userid');
	}
	
	protected function checkListId($listId) {
		$userId = $this->getUserId();
		$list = TaskListQuery::create()->filterById($listId)->findOne();
		if (empty($list)) {
			return false;
		}
		return ((boolean) ($userId === $list->getUserId()));
	}
	
}