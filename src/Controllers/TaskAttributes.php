<?php

namespace Gtd\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

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
	
}