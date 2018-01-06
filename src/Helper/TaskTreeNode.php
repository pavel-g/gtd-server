<?php

namespace Gtd\Helper;

class TaskTreeNode extends TreeNode {
	
	public function setName($name) {
		if ($name === 'root') {
			$this->name = $name;
		}
	}
	
	public function getName() {
		if ($this->name === 'root') {
			return $this->name;
		}
		$value = $this->getValue();
		return (($value) ? $value->getId() : null);
	}
	
	protected function valueToArray() {
		$value = $this->getValue();
		if ($value !== null) {
			return $value->toArray();
		}
	}
	
}