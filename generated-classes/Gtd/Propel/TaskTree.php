<?php

namespace Gtd\Propel;

use Gtd\Propel\Base\TaskTree as BaseTaskTree;
use \Gtd\Propel\TaskTreeQuery;

/**
 * Skeleton subclass for representing a row from the 'tasks' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class TaskTree extends BaseTaskTree
{
	
	public function getFullPath()
	{
		$path = $this->getPath();
		if (empty($path)) {
			return ((string) ($this->getId()));
		} else {
			return $path . '/' . $this->getId();
		}
	}
	
	public function setParentId($v)
	{
		parent::setParentId($v);
		$this->updateSelfPath($v);
		return $this;
	}
	
	protected function updateSelfPath($v)
	{
		if ($v === null) {
			$this->setPath('');
			return;
		}
		
		$parent = TaskTreeQuery::create()->findById($v);
		
		if (empty($parent)) {
			throw new \Exception('Parent not exists');
		}
		
		$path = $parent->getPath();
		if ($path !== '') {
			$path .= '/';
		}
		$path .= $parent->getId();
		
		$this->setPath($path);
	}
	
}
