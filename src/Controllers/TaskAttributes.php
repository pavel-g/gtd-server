<?php

namespace Gtd\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Propel\Runtime\Map\TableMap;
use Gtd\Propel\AttributeTypesQuery;

class TaskAttributes
{
	
	/**
	 * @var ContainerInterface
	 */
	protected $container;
	
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}
	
	public function getAction(Request $request, Response $response, $args)
	{
		$data = null;
		return $response->withJson(['success' => true, 'data' => $data]);
	}
	
	public function saveAction(Request $request, Response $response, $args)
	{
		return $response->withJson(['success' => true]);
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
	
}