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
use \Gtd\TaskUpdater;
use Propel\Runtime\Map\TableMap;
use Gtd\TaskFinder;

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
		$tasks = TaskTreeQuery::create()->filterByListId($listId)->filterByParentId($parentId)->filterByRemoved(null)->find();
		$data = $tasks->toArray(null, false, TableMap::TYPE_FIELDNAME);
		for( $i = 0; $i < count($data); $i++ ) {
			$data[$i]['leaf'] = !$data[$i]['has_children'];
		}
		return $response->withJson(['success' => true, 'data' => $data]);
	}
	
	public function createTaskAction(Request $request, Response $response, $args) {
		$body = new BodyParser();
		$body->setBody($request->getParsedBody());
		$listId = $request->getQueryParam('list_id', null);
		if (!$this->checkListId($listId)) {
			throw new \Exception('Low access level');
		}
		$parentId = $body->getParam('parent_id', null);
		$parent = $this->getParent($parentId);
		if ($parent === null) {
			$parentId = null;
		}
		$task = new TaskTreeRecord();
		$task->setListId($listId);
		$task->setTitle($body->getParam('title'));
		$task->setDescription($body->getParam('description'));
		$task->setCreated(new \DateTime('now'));
		$task->setDue($body->getParam('due'));
		$task->setPath($this->getPath($parent));
		$task->save();
		$task->setSmartParentId($parentId);
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
	
	public function updateTaskAction(Request $request, Response $response, $args) {
		$listId = $request->getQueryParam('list_id', null);
		if ($listId === null) {
			throw new \Exception('Undefined list_id');
		}
		if (!$this->checkListId($listId)) {
			throw new \Exception('Low access level');
		}
		$body = new BodyParser();
		$body->setBody($request->getParsedBody());
		$id = $body->getParam('id', null);
		if ($id === null) {
			throw new \Exception('Undefined id');
		}
		$task = TaskTreeQuery::create()->findPk($id);
		$updater = new TaskUpdater();
		$updater->update($body, $task);
		return $response->withJson(['success' => true]);
	}
	
	/**
	 * Поиск задач
	 * 
	 * Обработчик запроса /tree/find
	 * 
	 * Принимает в качестве параметров:
	 * 
	 * * list_id - идентификатор списка (обязательный параметр)
	 * * id - идентификатор задачи
	 * * title - название задачи
	 * 
	 * Для выполнения так же должен быть указан либо id, либо title
	 * 
	 * Возвращает массив с результатми поиска в виде:
	 * 
	 * <pre>
	 * {
	 *     "success": true,
	 *     "data": [
	 *         {},
	 *         {}
	 *     ]
	 * }
	 * </pre>
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param mixed $args
	 * @return Response
	 */
	public function findAction(Request $request, Response $response, $args)
	{
		$listId = $request->getQueryParam('list_id', null);
		$this->requireListId($listId);
		
		$title = $request->getQueryParam('title', null);
		$id = $request->getQueryParam('id', null);
		
		$finder = new TaskFinder($listId);
		$data = $finder->find([
			'id' => $id,
			'title' => $title
		]);
		
		return $response->withJson(['success' => true, 'data' => $data]);
	}
	
	/**
	 * Remove task
	 * 
	 * Handler for url /tree/remove
	 * 
	 * Params:
	 * 
	 * * list_id (integer) - required
	 * * id (integer) - required
	 * 
	 * Return json:
	 * 
	 * <pre>
	 * {"success": true}
	 * </pre>
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param mixed $args
	 * @return Response
	 */
	public function removeAction(Request $request, Response $response, $args)
	{
		$listId = $request->getQueryParam('list_id', null);
		$this->requireListId($listId);
		
		$body = new BodyParser();
		$body->setBody($request->getParsedBody());
		
		$id = $body->getInt('id');
		
		if (!is_numeric($id)) {
			throw new \Exception('Undefined id');
		}
		$id = (integer) $id;
		
		$task = TaskTreeQuery::create()->filterByListId($listId)->filterById($id)->findOne();
		if ($task === null) {
			return $response->withJson(['success' => false, 'message' => 'Task not exists']);
		}
		
		$task->smartRemove();
		
		return $response->withJson(['success' => true]);
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
	
	/**
	 * @param integer $listId
	 * @throws \Exception
	 */
	protected function requireListId($listId)
	{
		$userId = $this->getUserId();
		$list = TaskListQuery::create()->findPk($listId);
		if (empty($list) || $userId !== $list->getUserId()) {
			throw new \Exception('Low access level');
		}
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