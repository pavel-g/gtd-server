<?php

namespace Gtd\Parser;

use \Gtd\Util;

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
	
	public function getListId() {
		$id = $this->body['list_id'];
		return ((integer) $id);
	}
	
	public function getTitle() {
		return $this->body['title'];
	}
	
	public function getDescription() {
		return $this->body['description'];
	}
	
	protected function checkBody() {
		return ((boolean) (
			$this->checkParentId() &&
			$this->checkListId() &&
			$this->checkTitle() &&
			$this->checkDescription()
		));
	}
	
	protected function checkParentId() {
		$body = $this->body;
		return ((boolean) (
			array_key_exists('parent_id', $body) &&
			Util::checkNumericOrNull($body['parent_id'])
		));
	}
	
	protected function checkListId() {
		$body = $this->body;
		return ((boolean) (
			array_key_exists('list_id', $body) &&
			is_numeric($body['list_id'])
		));
	}
	
	protected function checkTitle() {
		$body = $this->body;
		return ((boolean) (
			array_key_exists('title', $body) &&
			is_string($body['title'])
		));
	}
	
	protected function checkDescription() {
		$body = $this->body;
		return ((boolean) (
			array_key_exists('description', $body) &&
			Util::checkStringOrNull($body['description'])
		));
	}
	
}