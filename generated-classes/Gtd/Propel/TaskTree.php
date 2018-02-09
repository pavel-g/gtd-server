<?php

namespace Gtd\Propel;

use Gtd\Propel\Base\TaskTree as BaseTaskTree;
use \Gtd\Propel\TaskTreeQuery;
use Propel\Runtime\Map\TableMap;

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
	
	protected $oldParent = null;
	
	protected $children = null;
	
	protected $oldValues = null;
	
	public function getFullPath()
	{
		$path = $this->getPath();
		if (empty($path)) {
			return ((string) ($this->getId()));
		} else {
			return $path . '/' . $this->getId();
		}
	}
	
	public function setSmartParentId($v)
	{
		$this->oldValues = $this->toArray(TableMap::TYPE_FIELDNAME);
		$this->oldValues['full_path'] = $this->getFullPath();
		
		if ($this->oldValues['parent_id'] !== null) {
			$this->oldParent = TaskListQuery::create()->findPk($this->oldValues['parent_id']);
		} else {
			$this->oldParent = null;
		}
		
		$this->children = TaskTreeQuery::create()->findAllChildren($this);
		
		$this->setParentId($v);
		$this->updateSelfPath($v);
		$this->save();
		$this->updateNewParentHasChildren();
		$this->updateOldParentHasChildren();
		$this->updateChildrenPath();
		
		$this->oldValues = null;
		$this->oldParent = null;
		$this->children = null;
		
		return $this;
	}
	
	public function getParent()
	{
		$id = $this->getParentId();
		if ($id === null) {
			return null;
		}
		return TaskTreeQuery::create()->findPk($id);
	}
	
	protected function updateOldParentHasChildren()
	{
		$parent = $this->oldParent;
		if (empty($parent)) {
			return;
		}
		$children = TaskTreeQuery::create()->findChildren($parent);
		$parent->setHasChildren(!empty($children));
		$parent->save();
	}
	
	protected function updateNewParentHasChildren()
	{
		$parent = $this->getParent();
		if ($parent === null) {
			return;
		}
		$parent->setHasChildren(true);
		$parent->save();
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
	
	protected function updateChildrenPath()
	{
		if (empty($this->children)) {
			return;
		}
		$fullPath = $this->getFullPath();
		for( $i = 0; $i < count($this->children); $i++ ) {
			$child = $this->children[$i];
			$childPath = $child->getPath();
			if ($childPath === $this->oldValues['full_path']) {
				$child->setPath($fullPath);
			} else if (strpos($childPath, $this->oldValues['full_path'] . '/') === 0) {
				$child->setPath(substr_replace($childPath, $fullPath . '/', 0, strlen($this->oldValues['full_path'] . '/')));
			}
			$child->save();
		}
	}
	
}
