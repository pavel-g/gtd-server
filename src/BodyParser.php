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
	
	public function hasIntOrNull($key)
	{
		if (!array_key_exists($key, $this->body)) {
			return false;
		}
		return ($this->body[$key] === null || is_numeric($this->body[$key]));
	}
	
	public function getInt($key, $default = null) {
		if (!$this->hasParam($key)) {
			return $default;
		}
		if (is_numeric($this->body[$key])) {
			return ((integer) $this->body[$key]);
		}
		return $default;
	}
	
	/**
	 * @param string $key
	 * @param mixed $default
	 * @return string
	 */
	public function getStringFromJson($key, $default = null) {
		if (!$this->hasParam($key)) {
			return $default;
		}
		if (is_string($this->body[$key])) {
			return $this->body[$key];
		}
		if (\is_array($this->body[$key])) {
			return json_encode($this->body[$key]);
		}
		return $default;
	}
	
	protected function transform() {}
	
}