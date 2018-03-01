<?php

namespace Gtd\Cli;

use Gtd\Propel\TaskTree;
use Gtd\Propel\TaskTreeQuery;
use Gtd\Propel\UserQuery;
use Gtd\Propel\User;
use Gtd\Propel\TaskListQuery;
use Gtd\Propel\TaskList;

class TestData {
	
	/**
	 * @var TaskTree[]|null
	 */
	protected $createdTasks = null;

	public function __construct() {
		$this->createdTasks = [];
	}

	public function install() {
		echo "Test data loading...";
		$this->prepareTestUser();
		echo " DONE" . PHP_EOL;
	}

	public function prepareTestUser() {
		$user = UserQuery::create()->findOneByName('user');
		if ($user) {
			$this->deleteUser($user);
		}

		$user = new User();
		$user->setName('user');
		$user->setPass('123');
		$user->save();

		$homeList = new TaskList();
		$homeList->setUserId($user->getId());
		$homeList->setTitle('Дом');
		$homeList->save();

		$workList = new TaskList();
		$workList->setUserId($user->getId());
		$workList->setTitle('Работа');
		$workList->save();

		$homeListId = $homeList->getId();

		$this->createTasks($homeListId, [
			['title' => 'task1'],
			['title' => 'task1.1', 'parent' => 'task1'],
			['title' => 'task1.1.1', 'parent' => 'task1.1'],
			['title' => 'task2'],
			['title' => 'task2.1', 'parent' => 'task2'],
			['title' => 'task3'],
			['title' => 'task3.1', 'parent' => 'task3'],
			['title' => 'task3.2', 'parent' => 'task3'],
			['title' => 'task4']
		]);
	}

	/**
	 * @param integer $listId
	 * @param array $params
	 * @throws \Propel\Runtime\Exception\PropelException
	 */
	protected function createTask($listId, $params) {
		$task = new TaskTree();
		$task->setTitle($params['title']);
		$task->setListId($listId);
		$task->save();
		$this->createdTasks[] = $task;
		if (!isset($params['parent'])) {
			return;
		}
		$parent = $this->findTaskByTitle($params['parent']);
		if ($parent) {
			$task->setSmartParentId($parent->getId());
		}
	}

	protected function createTasks($listId, $params) {
		for ($i = 0; $i < count($params); $i++) {
			$this->createTask($listId, $params[$i]);
		}
	}

	/**
	 * @param string $title
	 * @return TaskTree|null
	 */
	protected function findTaskByTitle($title) {
		for ($i = 0; $i < count($this->createdTasks); $i++) {
			/** @var TaskTree $task */
			$task = $this->createdTasks[$i];
			if ($task->getTitle() === $title) {
				return $task;
			}
		}
		return null;
	}
	
	/**
	 * @param User $user
	 * @throws \Propel\Runtime\Exception\PropelException
	 */
	protected function deleteUser($user) {
		$userId = $user->getId();
		$lists = TaskListQuery::create()->findByUserId($userId);
		if (!empty($lists)) {
			for ($i = 0; $i < count($lists); $i++) {
				$list = $lists[$i];
				$listId = $list->getId();
				TaskTreeQuery::create()->findByListId($listId)->delete();
				$list->delete();
			}
		}
		$user->delete();
	}

}
