<?php

namespace Gtd\Helper;

class Path {
	
	private $path;
	
	public function __construct($path) {
		$this->path = explode('/', $path);
	}
	
	public function getFullPath() {
		return implode('/', $this->path);
	}
	
	public function getPath() {
		$path = $this->path;
		if (count($path) <= 1) {
			return '';
		}
		array_pop($path);
		return implode('/', $path);
	}
	
	public function getName() {
		$count = count($this->path);
		return $this->path[$count - 1];
	}
	
	public function getLevel() {
		return count($this->path);
	}
	
	public function shiftedPath() {
		$path = $this->path;
		if (count($path) <= 1) {
			return '';
		}
		array_shift($path);
		return implode('/', $path);
	}
	
	public function getParts() {
		return $this->path;
	}
	
	public function isPartOf($path) {
		$parts = $path->getParts();
		for( $i = 0; $i < count($this->path); $i++ ) {
			if ($this->path[$i] !== $parts[$i]) {
				return false;
			}
		}
		return true;
	}
	
	public function replace($from, $to) {
		if (!$from->isPartOf($this)) {
			return false;
		}
		$current = $this->path;
		$toParts = $to->getParts();
		array_splice($this->path, 0, count($from->getParts()), $toParts);
		return true;
	}
	
}