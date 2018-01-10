<?php

namespace Gtd\Helper;

use \Gtd\Util;

class TaskTreeNode extends TreeNode {
	
	public function getName() {
		$value = $this->getValue();
		if ($value) {
			$id = $value->getId();
			return (string) $id;
		}
		return parent::getName();
	}
	
	protected function valueToArray() {
		$value = $this->getValue();
		if ($value !== null) {
			return Util::recordKeysCamelCaseToUnderscore($value->toArray());
		}
		return $value;
	}
	
}