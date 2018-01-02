<?php

namespace Gtd\Model;

class TaskTree {
	
	protected $listId = null;
	
	protected $title = null;
	
	protected $description = null;
	
	protected $created = null;
	
	protected $due = null;
	
	protected $removed = null;
	
	protected $completed = null;
	
	protected $parentId = null;
	
	protected $id = null;
	
	public function setListId($listId) {
		if (!is_numeric($listId)) {
			return false;
		}
		$this->listId = (integer) $listId;
		return true;
	}
	
	public function getListId() {
		return $this->listId;
	}
	
	public function setTitle($title) {
		if (is_string($title) && strlen($title) > 0) {
			$this->title = $title;
			return true;
		}
		return false;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setDescription($desc) {
		if (is_string($desc) && stdlen($desc) > 0) {
			$this->description = $desc;
		} else if ($desc === null) {
			$this->description = null;
		} else {
			return false;
		}
		return true;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function setCreated($date) {
		return $this->setDateField($date, 'created', false);
	}
	
	public function getCreated() {
		return $this->created;
	}
	
	public function setDue($date) {
		return $this->setDateField($date, 'due', false);
	}
	
	public function getDue() {
		return $this->due;
	}
	
	public function setRemoved($date) {
		return $this->setDateField($date, 'removed', false);
	}
	
	public function getRemoved() {
		return $this->removed;
	}
	
	public function setCompleted($date) {
		$this->setDateField($date, 'completed', false);
	}
	
	public function getCompleted() {
		return $this->completed;
	}
	
	public function setParentId($id) {
		if ($id === null) {
			$this->parentId = null;
			return true;
		} else if (is_numeric($id)) {
			$this->parentId = (integer) $id;
			return true;
		}
		return false;
	}
	
	public function getParentId() {
		return $this->parentId;
	}
	
	public function setId($id) {
		if ($id === null) {
			$this->id = null;
			return true;
		} else if (is_numeric($id)) {
			$this->id = (integer) $id;
			return true;
		}
		return false;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function toArray() {
		return [
			'list_id' => $this->getListId(),
			'title' => $this->getTitle(),
			'description' => $this->getDescription(),
			'created' => $this->getCreated(),
			'due' => $this->getDue(),
			'removed' => $this->getRemoved(),
			'completed' => $this->getCompleted(),
			'parent_id' => $this->getParentId(),
			'id' => $this->getId()
		];
	}
	
	protected function setDateField($date, $field, $required) {
		if (!$required && $date === null) {
			$this->$field = null;
			return true;
		}
		if (is_string($date)) {
			$d = \Gtd\Util::stringToDate($date);
		} else {
			$d = $date;
		}
		if ($d instanceof \DateTime) {
			$this->$field = $d;
			return true;
		}
		return false;
	}
	
}