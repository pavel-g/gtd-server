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
	
}