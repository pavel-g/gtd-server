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
		$this->oldParentId = $this->getParentId();
		parent::setParentId($v);
		$this->updateSelfPath($v);
		$this->updateNewParentHasChildren();
		$this->updateOldParentHasChildren();
		return $this;
	}
	
	public function updateOldParentHasChildren()
	{
		if ($this->isModified()) {
			return false;
		}
		$parent = TaskTreeQuery::create()->findPk($this->oldParentId);
		if (empty($parent)) {
			return false;
		}
		$children = TaskTreeQuery::create()->findChildren($parent);
		$parent->setHasChildren(!empty($children));
		$parent->save();
		return true;
	}
	
	public function updateNewParentHasChildren()
	{
		if ($this->isModified()) {
			return false;
		}
		$parentId = $this->getParentId();
		if ($parentId === null) {
			return false;
		}
		$parent = TaskTreeQuery::create()->findPk($parentId);
		if (empty($parent)) {
			throw new \Exception('Parent not exists');
		}
		$parent->setHasChildren(true);
		$parent->save();
		return true;
	}
	
	protected function updateSelfPath($v)
	{
		if ($v === null) {
			$this->setPath('');
			return;
		}
		
		$parent = TaskTreeQuery::create()->findPk($v);
		
		if (empty($parent)) {
			throw new \Exception('Parent not exists');
		}
		
		$path = $parent->getFullPath();
		
		$this->setPath($path);
	}
	
}
