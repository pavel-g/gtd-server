<?php

namespace Gtd\Controllers;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Propel\Runtime\Map\TableMap;
use Gtd\Propel\AttributeTypesQuery;
use Gtd\GetSessionUserId;
use Gtd\Propel\UserQuery;
use Gtd\Propel\TaskTreeQuery;
use Gtd\Propel\TaskListQuery;
use Gtd\BodyParser;
use Gtd\AttributeTypes as AttributeTypesConsts;
use Gtd\Propel\AttributesQuery;
use Gtd\Propel\Attributes;

class TaskAttributes
{
	
	use GetSessionUserId;
	
	/**
	 * @var ContainerInterface
	 */
	protected $container;
	
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}
	
	/**
	 * Attributes list
	 * 
	 * Url: /attributes/get
	 * 
	 * Method: GET
	 * 
	 * Parameters in url:
	 * 
	 * * task_id (integer)
	 * 
	 * Response:
	 * 
	 * ```json
	 * {
	 *     "success": true,
	 *     "data": [
	 *         {
	 *             "id": 1,
	 *             "task_id": 1,
	 *             "type": "<string>",
	 *             "value": "<json>"
	 *         }
	 *     ]
	 * }
	 * ```
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param mixed $args
	 * @return void
	 */
	public function getAction(Request $request, Response $response, $args)
	{
		$taskId = $this->getTaskId($request);
		$this->checkTaskId($taskId);
		$attributes = AttributesQuery::create()->findByTaskId($taskId);
		$data = [];
		foreach( $attributes as $attribute ) {
			$data[] = $this->serializeAttributeForClients($attribute->toArray(TableMap::TYPE_FIELDNAME));
		}
		return $response->withJson(['success' => true, 'data' => $data]);
	}
	
	/**
	 * Save attribute
	 * 
	 * Url: /attributes/save
	 * 
	 * Method: POST
	 * 
	 * Parameters in url
	 * 
	 * * task_id (integer)
	 * 
	 * Body json format:
	 * 
	 * ```json
	 * [
	 *     {
	 *         "type": "ATTRIBUTE_HASHTAGS",
	 *         "value": [
	 *             "в_городе",
	 *             "студенческая",
	 *             "кольцово"
	 *         ]
	 *     }
	 * ]
	 * ```
	 * 
	 * Response:
	 * 
	 * ```json
	 * {
	 *     "success": true
	 * }
	 * ```
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param mixed $args
	 * @return void
	 */
	public function saveAction(Request $request, Response $response, $args)
	{
		$taskId = $this->getTaskId($request);
		$this->checkTaskId($taskId);
		$body = $request->getParsedBody();
		$data = [];
		foreach( $body as $key => $value ) {
			$attribute = $this->saveAttribute($taskId, $value);
			$data[] = $this->serializeAttributeForClients($attribute->toArray(TableMap::TYPE_FIELDNAME));
		}
		return $response->withJson(['success' => true, 'data' => $data]);
	}
	
	public function deleteAction(Request $request, Response $response, $args)
	{
		return $response->withJson(['success' => true]);
	}
	
	/**
	 * Attributes types
	 * 
	 * Url: /api/attributes/types
	 * 
	 * Method: GET
	 * 
	 * Return json with data:
	 * 
	 * ```
	 * [
	 *     {
	 *         "code": "STRING_CONSTANT",
	 *         "name": "Attribute description",
	 *         "type": "STRING_CONSTANT"
	 *     }
	 * ]
	 * ```
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param [type] $args
	 * @return void
	 */
	public function typesAction(Request $request, Response $response, $args)
	{
		$data = AttributeTypesQuery::create()->find();
		return $response->withJson([
			'success' => true,
			'data' => $data->toArray(null, false, TableMap::TYPE_FIELDNAME)
		]);
	}
	
	/**
	 * Get task_id
	 *
	 * @param Request $request
	 * @return integer|null
	 */
	protected function getTaskId(Request $request)
	{
		$taskId = $request->getQueryParam('task_id');
		if (is_numeric($taskId)) {
			return (integer) $taskId;
		}
		return null;
	}
	
	/**
	 * Check task_id
	 *
	 * @param integer|null $taskId
	 * @throws \Exception
	 */
	protected function checkTaskId($taskId)
	{
		if ($taskId === null) {
			throw new \Exception('task_id undefined');
		}
		$task = TaskTreeQuery::create()->findPk($taskId);
		if (empty($task)) {
			throw new \Exception('task not exists');
		}
		$list = TaskListQuery::create()->findPk($task->getListId());
		if (empty($list)) {
			throw new \Exception('List not exists');
		}
		$user = UserQuery::create()->findPk($list->getUserId());
		if (empty($user)) {
			throw new \Exception('User not exists');
		}
		if ($user->getId() != $this->getUserId()) {
			throw new \Exception('Low access level');
		}
	}
	
	/**
	 * @param integer $taskId
	 * @param array $params
	 * @throws \Exception
	 * @return Attributes|null
	 */
	protected function saveAttribute($taskId, $params)
	{
		$bodyParser = new BodyParser();
		$bodyParser->setBody($params);
		$type = $bodyParser->getParam('type');
		$fieldValue = AttributeTypesConsts::getValueFieldByAttributeType($type);
		if ($fieldValue === null) {
			throw new \Exception('Wrong type of attribute');
		}
		$attr = AttributesQuery::create()->filterByTaskId($taskId)->filterByType($type)->findOne();
		if (empty($attr)) {
			$insert = true;
			$attr = new Attributes();
		} else {
			$update = true;
		}
		$value = $bodyParser->getParam('value');
		if (is_array($value)) {
			$value = json_encode($value);
		}
		$valueParams = [
			$fieldValue => $value
		];
		$attr->fromArray($valueParams, TableMap::TYPE_FIELDNAME);
		if ($insert) {
			$attr->setType($type);
			$attr->setTaskId($taskId);
		}
		$attr->save();
		return $attr;
	}
	
	/**
	 * @param array $attr
	 * @return array
	 */
	protected function serializeAttributeForClients($attr)
	{
		$valueField = AttributeTypesConsts::getValueFieldByAttributeType($attr['type']);
		$data = [
			'id' => $attr['id'],
			'type' => $attr['type'],
			'task_id' => $attr['task_id'],
			'value' => $attr[$valueField]
		];
		return $data;
	}
	
}