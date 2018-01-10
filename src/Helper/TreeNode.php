<?php

namespace Gtd\Helper;

class TreeNode {
	
	private $value;
	private $children;
	private $parent;
	private $name;
	
	public function __construct() {
		$this->children = [];
		$this->value = null;
		$this->parent = null;
		$this->name = '';
	}
	
	public function toArray($childrenKey = null) {
		$children = [];
		for( $i = 0; $i < count($this->children); $i++ ) {
			$children[] = $this->children[$i]->toArray($childrenKey);
		}
		if ($childrenKey === null) {
			$res = [
				'value' => $this->valueToArray(),
				'children' => $children
			];
		} else {
			$res = $this->valueToArray();
			$res[$childrenKey] = $children;
		}
		return $res;
	}
	
	public function isRoot() {
		return ((boolean) ($this->parent === null));
	}
	
	public function hasChildren() {
		return ((boolean) (count($this->children) > 0));
	}
	
	public function setValue($value) {
		$this->value = $value;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function setParent(TreeNode $parent) {
		$this->parent = $parent;
	}
	
	public function clearParent() {
		$this->parent = null;
	}
	
	public function getParent() {
		return $this->parent;
	}
	
	public function setChildren(array $children) {
		$this->children = $children;
	}
	
	public function clearChildren() {
		$this->children = [];
	}
	
	public function appendChild($child) {
		$child->setParent($this);
		$this->children[] = $child;
	}
	
	public function &getChildren() {
		return $this->children;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = (string) $name;
	}
	
	public function getPath($full = false) {
		if (!$this->isRoot()) {
			$path = $this->parent->getPath(true);
		} else {
			$path = '';
		}
		if ($full) {
			if ($path !== '') {
				$path = $path . '/';
			}
			$path = $path . $this->getName();
		}
		return $path;
	}
	
	public function getChildByName($name) {
		for( $i = 0; $i < count($this->children); $i++ ) {
			$child = $this->children[$i];
			if ($child->getName() == $name) {
				return $child;
			}
		}
		return null;
	}
	
	public function setChildByName($name, $child) {
		for( $i = 0; $i < count($this->children); $i++ ) {
			if ($this->children[$i]->getName() == $name) {
				$this->children[$i] = $child;
				return;
			}
		}
		$this->children[] = $child;
	}
	
	public function getChildByPath($path) {
		$path = $this->parsePath($path);
		if (count($path) <= 0) {
			return null;
		}
		$name = array_shift($path);
		$child = $this->getChildByName($name);
		if ($child === null) {
			return null;
		}
		if (count($path) <= 0) {
			return $child;
		}
		return $child->getChildByPath($path);
	}
	
	public function setChildByPath($path, $child) {
		$path = $this->parsePath($path);
		$name = array_pop($path);
		$node = $this->createPath($path);
		return $node->setChildByName($name, $child);
	}
	
	protected function appendEmptyNode($name) {
		$node = new static();
		$node->setName($name);
		$this->appendChild($node);
		return $node;
	}
	
	protected function createPath($path) {
		$path = $this->parsePath($path);
		if (count($path) <= 0) {
			return $this;
		}
		$name = array_shift($path);
		$node = $this->getChildByName($name);
		if ($node === null) {
			$node = $this->appendEmptyNode($name);
		}
		return $node->createPath($path);
	}
	
	protected function parsePath($path) {
		if (is_string($path)) {
			return explode('/', $path);
		}
		return $path;
	}
	
	protected function valueToArray() {
		return $this->value;
	}
	
}