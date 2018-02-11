<?php

namespace Gtd;

use Gtd\Propel\TaskTreeQuery;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\ActiveQuery\Criteria;

class TaskFinder
{
	
	protected $listId = null;
	
	/**
	 * @param integer $listId
	 */
	public function __construct($listId)
	{
		$this->listId = $listId;
	}
	
	/**
	 * Поиск
	 *
	 * @param array $params
	 * @throws \Exception
	 * @return array
	 */
	public function find($params)
	{
		$id = $params['id'];
		$title = $params['title'];
		
		if (is_numeric($id)) {
			$id = (integer) $id;
			return $this->findById($id);
		} else if (!empty($title)) {
			return $this->findByTitle($title);
		}
		
		throw new \Exception('Undefined params id or title');
	}
	
	/**
	 * Поиск по id
	 * 
	 * @param integer $id
	 * @return array
	 */
	protected function findById($id)
	{
		$task = TaskTreeQuery::create()->findPk($id);
		if ($task->getListId() !== $this->listId) {
			return [];
		}
		return [$task->toArray(TableMap::TYPE_FIELDNAME)];
	}
	
	/**
	 * Поиск по title
	 *
	 * @param string $title
	 * @return array
	 */
	protected function findByTitle($title)
	{
		$words = explode(' ', $title);
		$query = implode('%', $words);
		$query = '%' . $query . '%';
		$tasks = TaskTreeQuery::create()
		         ->filterByListId($this->listId)
		         ->filterByTitle($query, Criteria::LIKE)
		         ->find();
		return $tasks->toArray(null, false, TableMap::TYPE_FIELDNAME);
	}
	
}