<?php

namespace Gtd\Helper;

use \Gtd\Propel\TaskTreeQuery;
use \Gtd\Propel\TaskListQuery;
use \Gtd\Helper\Path;
use \Gtd\Helper\TreeNode;

class TreeBuilder {
	
	protected $listId = null;
	
	protected $tree = null;
	
	public function __construct() {
		$this->listId = null;
		$this->initEmptyTree();
	}
	
	public function setListId($listId) {
		$this->listId = $listId;
	}
	
	public function getListId() {
		return $this->listId;
	}
	
	public function initEmptyTree() {
		$this->tree = new TaskTreeNode();
		$this->tree->setName('root');
	}
	
	public function buildTree() {
		$this->initEmptyTree();
		$listId = $this->listId;
		$tasks = TaskTreeQuery::create()->filterByListId($listId)->find();
		for( $i = 0; $i < count($tasks); $i++ ) {
			$task = $tasks[$i];
			$path = $this->getPathFromTask($task);
			$node = new TaskTreeNode();
			$node->setValue($task);
			$node->setName($task->getId());
			$this->tree->setChildByPath($path, $node);
		}
	}
	
	public function getTree() {
		$this->buildTree();
		return $this->tree->toArray('children');
	}
	
	protected function getPathFromTask($task) {
		$path = $task->getPath();
		$name = $task->getId();
		if ($path !== '') {
			$path = $path . '/';
		}
		return 'root/' . $path . $name;
	}
	
}