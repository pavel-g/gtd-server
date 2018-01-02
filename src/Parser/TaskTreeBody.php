<?php

namespace Gtd\Parser;

class TaskTreeBody {
	
	protected $body;
	
	public function __construct() {
		$this->body = [];
	}
	
	public function setBody($body) {
		$old = $this->body;
		$this->body = $body;
		if (!$this->checkBody()) {
			$this->body = $old;
			return false;
		}
		return true;
	}
	
	public function getParentId() {
		$id = $this->body['parent_id'];
		if ($id === null) {
			return null;
		} else {
			return (integer) $id;
		}
	}
	
	protected function checkBody() {
		return ((boolean) (
			$this->checkParentId()
		));
	}
	
	protected function checkParentId() {
		$body = $this->body;
		return ((boolean) (
			array_key_exists('parent_id', $body) &&
			(
				is_numeric($body['parent_id']) ||
				$body['parent_id'] === null
			)
		));
	}
	
}