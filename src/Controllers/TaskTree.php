<?php

namespace Gtd\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Container\ContainerInterface;

class TaskTree {
	
	protected $container;
	
	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}
	
	public function getAllAction(Request $request, Response $response, $args) {
		return $response->withJson(['success' => true, 'data' => null]);
	}
	
	public function createTaskAction(Request $request, Response $response, $args) {
		return $response->withJson(['success' => false]);
	}
	
	protected function getSession() {
		return $this->container['session'];
	}
	
	protected function getUserId() {
		$session = $this->getSession();
		return $session->get('userid');
	}
	
}