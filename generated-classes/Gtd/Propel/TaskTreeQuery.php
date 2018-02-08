<?php

namespace Gtd\Propel;

use Gtd\Propel\Base\TaskTreeQuery as BaseTaskTreeQuery;
use \Propel\Runtime\ActiveQuery\Criteria;

/**
 * Skeleton subclass for performing query and update operations on the 'tasks' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class TaskTreeQuery extends BaseTaskTreeQuery
{
	
	public function findAllChildren($task)
	{
		return $this->filterAllChildren($task)->find();
	}
	
	public function filterAllChildren($task)
	{
		$path = $task->getFullPath();
		$this->condition('fullequals', 'tasks.Path LIKE ?', $path);
		$this->condition('partequals', 'tasks.Path LIKE ?', $path . '/%');
		$this->where(['fullequals', 'partequals'], Criteria::LOGICAL_OR);
		return $this;
	}
	
	public function findChildren($task)
	{
		return $this->filterChildren($task)->find();
	}
	
	public function filterChildren($task)
	{
		$path = $task->getFullPath();
		return $this->filterByPath($path, Criteria::LIKE);
	}
	
	public function findParent($task)
	{
		$parentId = $task->getParentId();
		if ($parentId === null) {
			return null;
		}
		return $this->findPk($parentId);
	}
	
}
