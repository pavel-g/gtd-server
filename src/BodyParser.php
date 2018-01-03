<?php

namespace Gtd;

class BodyParser {
	
	protected $body;
	
	public function __construct() {
		$this->body = [];
	}
	
	public function setBody($body) {
		$this->body = $body;
		$this->transform();
	}
	
	public function hasParam($key) {
		return array_key_exists($key, $this->body);
	}
	
	public function getParam($key, $default = null) {
		if ($this->hasParam($key)) {
			return $this->body[$key];
		} else {
			return $default;
		}
	}
	
	public function setParam($key, $value) {
		$this->body[$key] = $value;
		$this->transform();
	}
	
	protected function transform() {}
	
}