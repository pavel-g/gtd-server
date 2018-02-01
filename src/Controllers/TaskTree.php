<?php

namespace Gtd\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Container\ContainerInterface;
use \Gtd\Propel\TaskTree as TaskTreeRecord;
use \Gtd\Propel\TaskTreeQuery;
use \Gtd\BodyParser;
use \Gtd\Propel\TaskListQuery;
use \Gtd\Util;
use \Gtd\Helper\TreeBuilder;

class TaskTree {
	
	protected $container;
	
	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}
	
	public function getAllAction(Request $request, Response $response, $args) {
		$listId = $request->getQueryParam('list_id', null);
		if ($listId === null) {
			return $response->withJson(['success' => false, 'message' => 'Undefined list_id']);
		}
		if (!$this->checkListId($listId)) {
			return $response->withJson(['success' => false, 'message' => 'Low access level']);
		}
		$parentId = $request->getQueryParam('node', null);
		if ($parentId === 'root') {
			$parentId = null;
		}
		$tasks = TaskTreeQuery::create()->filterByListId($listId)->filterByParentId($parentId)->find();
		$data = Util::storeKeysCamelCaseToUnderscore($tasks->toArray());
		return $response->withJson(['success' => true, 'data' => $data]);
	}
	
	public function createTaskAction(Request $request, Response $response, $args) {
		$body = new BodyParser();
		$body->setBody($request->getParsedBody());
		$listId = $request->getQueryParam('list_id', null);
		if (!$this->checkListId($listId)) {
			return $response->withJson(['success' => false, 'message' => 'Low access level']);
		}
		$parentId = $body->getParam('parent_id', null);
		$parent = $this->getParent($parentId);
		if ($parent === null) {
			$parentId = null;
		}
		$task = new TaskTreeRecord();
		$task->setParentId($parentId);
		$task->setListId($listId);
		$task->setTitle($body->getParam('title'));
		$task->setDescription($body->getParam('description'));
		$task->setCreated(new \DateTime('now'));
		$task->setDue($body->getParam('due'));
		$task->setPath($this->getPath($parent));
		$task->save();
		$data = Util::recordKeysCamelCaseToUnderscore($task->toArray());
		return $response->withJson(['success' => true, 'data' => $data]);
	}
	
	public function getFullTreeAction(Request $request, Response $response, $args) {
		$listId = $request->getQueryParam('list_id', null);
		if ($listId === null) {
			return $response->withJson(['success' => false, 'message' => 'Undefined list_id']);
		}
		if (!$this->checkListId($listId)) {
			return $response->withJson(['success' => false, 'message' => 'Low access level']);
		}
		$treeBuilder = new TreeBuilder();
		$treeBuilder->setListId($listId);
		$data = $treeBuilder->getTree();
		if (is_array($data) && array_key_exists('children', $data)) {
			$data = $data['children'];
		}
		return $response->withJson(['success' => true, 'data' => $data]);
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
	
	protected function getParent($parentId) {
		if ($parentId === null) {
			return null;
		}
		$parent = TaskTreeQuery::create()->findPk($parentId);
		if (empty($parent)) {
			return null;
		}
		return $parent;
	}
	
	protected function getPath($parent) {
		if ($parent === null) {
			return '';
		}
		$path = $parent->getPath();
		if ($path === '') {
			return $parent->getId();
		}
		return $path . '/' . $parent->getId();
	}
	
}