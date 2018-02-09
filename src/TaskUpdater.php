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
		if (!$this->body->hasIntOrNull('parent_id')) {
			return;
		}
		$newParentId = $this->body->getInt('parent_id');
		$this->record->setSmartParentId($newParentId);
	}
	
	protected function changeCompleted() {
	}
	
}