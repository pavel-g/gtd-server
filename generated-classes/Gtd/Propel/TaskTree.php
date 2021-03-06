<?php

namespace Gtd\Propel;

use Gtd\Propel\Base\TaskTree as BaseTaskTree;
use \Gtd\Propel\TaskTreeQuery;
use Propel\Runtime\Map\TableMap;
use Gtd\RepeatTypes;

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
			$this->oldParent = TaskTreeQuery::create()->findPk($this->oldValues['parent_id']);
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
	
	/**
	 * @param boolean $value
	 * @return self
	 */
	public function setSmartCompleted($value)
	{
		if ($value === false) {
			$this->setCompleted(null);
			$this->save();
			return $this;
		} else if ($value !== true) {
			return $this;
		}
		
		$now = new \DateTime();
		
		$children = TaskTreeQuery::create()->findAllChildren($this);
		/** @var TaskTree $child */
		foreach ($children as &$child) {
			if (!$child->getCompleted()) {
				$child->setCompleted($now);
				$child->save();
			}
		}
		
		$this->setCompleted($now);
		$this->save();
		
		return $this;
	}
	
	/**
	 * @return self
	 */
	public function smartRemove()
	{
		$completed = $this->getCompleted();
		$removed = $this->getRemoved();
		if (!empty($removed) || !empty($completed)) {
			return;
		}
		$now = new \DateTime();
		
		$children = TaskTreeQuery::create()->filterByRemoved(null)->findAllChildren($this);
		foreach( $children as &$child ) {
			$child->setRemoved($now);
			$child->save();
		}
		
		$this->setRemoved($now);
		$this->save();
		
		return $this;
	}
	
	/**
	 * Parse repeat rule
	 * 
	 * Return assoc array:
	 * 
	 * ```
	 * [
	 *     'type' => '',
	 *     'interval' => 0
	 * ]
	 * ```
	 *
	 * @inheritDoc
	 * @return array|null
	 */
	public function getRepeatRule()
	{
		$value = parent::getRepeatRule();
		if (!is_string($value) || $value === '') {
			return null;
		}
		$value = json_decode($value, true);
		$type = $value['type'];
		$typesList = RepeatTypes::getRepeatTypes();
		if (!in_array($type, $typesList)) {
			throw new \Exception('Wrong value of "type"');
		}
		if ($type === RepeatTypes::BY_HAND_REPEAT) {
			return ['type' => $type];
		}
		$interval = $value['interval'];
		if (!is_numeric($interval)) {
			throw new \Exception('Wrong value of "interval"');
		}
		return ['type' => $type, 'interval' => ((int) $interval)];
	}
	
	/**
	 * @inheritDoc
	 * @param array|null $v
	 * @return void
	 */
	public function setRepeatRule($v)
	{
		if ($v === null) {
			return parent::setRepeatRule($v);
		}
		if (!is_array($v)) {
			throw new \Exception('Wrong value of repeat_rule field');
		}
		$typesList = RepeatTypes::getRepeatTypes();
		$res = [];
		if (isset($v['type']) && in_array($v['type'], $typesList)) {
			$res['type'] = $v['type'];
		} else {
			throw new \Exception('Wrong type of "type"');
		}
		if ($res['type'] === RepeatTypes::AUTO_REPEAT) {
			if (!is_numeric($v['interval'])) {
				throw new \Exception('Wrong type of "interval"');
			}
			$res['interval'] = $v['interval'];
		}
		return parent::setRepeatRule(json_encode($res));
	}
	
	protected function updateOldParentHasChildren()
	{
		$parent = $this->oldParent;
		if (empty($parent)) {
			return;
		}
		$children = TaskTreeQuery::create()->findChildren($parent);
		$hasChildren = ((boolean) (
			!empty($children) &&
			$children->count() > 0
		));
		$parent->setHasChildren($hasChildren);
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
