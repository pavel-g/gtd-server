<?php

namespace Gtd;

use \Gtd\Propel\TaskTreeQuery;
use \Gtd\Util;
use \Gtd\Helper\Path;
use \Propel\Runtime\Map\TableMap;
use \Propel\Runtime\ActiveQuery\Criteria;

class TaskUpdater {
	
	public function update($body, $record) {
		$this->body = $body;
		$this->record = $record;
		$this->move();
		$this->changeCompleted();
		$this->updateProperties();
		$this->record->save();
	}
	
	protected function updateProperties() {
		$body = $this->body;
		$keys = $this->getPropertiesForUpdate();
		$values = [];
		for( $i = 0; $i < count($keys); $i++ ) {
			$key = $keys[$i];
			if ($body->hasParam($key)) {
				$values[$key] = $body->getParam($key);
			}
		}
		$this->record->fromArray($values, TableMap::TYPE_FIELDNAME);
	}
	
	protected function getPropertiesForUpdate() {
		return [
			'title',
			'description',
			'due'
		];
	}
	
	protected function move() {
		$newParentId = $this->body->getInt('parent_id');
		if ($newParentId === null) {
			return;
		}
		$oldParentId = $this->record->getParentId();
		if ($newParentId !== $oldParentId) {
			/** @var Path $currentPath */
			$currentPath = Util::createPathFromTask($this->record);
			$newParent = TaskTreeQuery::create()->findPk($newParentId);
			$newParentPath = Util::createPathFromTask($newParent);
			if ($currentPath->isPartOf($newParentPath)) {
				throw new \Exception('Impossible move task into children');
			}
			$this->record->setParentId($newParentId);
			$newPath = $newParent->getPath();
			/** @var string $newPath */
			if ($newPath === '') {
				$newPath = (string) $newParentId;
			} else {
				$newPath = $newPath . '/' . $newParentId;
			}
			$this->record->setPath($newPath);
			$this->updateChildrenPath($currentPath->getPath(), $newParentPath->getFullPath());
		}
	}
	
	protected function updateChildrenPath($old, $new) {
		$listId = $this->record->getListId();
		$children = TaskTreeQuery::create()
		            ->filterByListId($listId)
		            ->filterByPath($old . '/%', Criteria::LIKE)
		            ->filterByPath($old, Criteria::NOT_LIKE)
		            ->find();
		$oldPath = new Path($old);
		$newPath = new Path($new);
		for( $i = 0; $i < count($children); $i++ ) {
			$child = $children[$i];
			$childPath = Util::createPathFromTask($child);
			$childPath->replace($oldPath, $newPath);
			$child->setPath($childPath->getPath());
			$child->save();
		}
	}
	
	protected function changeCompleted() {
		if (!$this->body->hasParam('completed')) {
			return;
		}
		$completed = $this->body->getParam('completed');
		if ($completed === null) {
			$this->record->setCompleted(null);
		} else {
			$now = new \DateTime();
			$this->record->setCompleted($now);
		}
	}
	
}