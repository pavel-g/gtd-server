<?php

namespace Gtd\Attributes;

use Gtd\AttributeTypes;

class Json implements IAttribute
{
	
	/**
	 * @inheritDoc
	 * @return boolean
	 */
	public function setValue($value)
	{
		if (gettype($value) === 'string') {
			$value = json_decode($value, true);
		}
		if (gettype($value) !== 'array') {
			return false;
		}
		if (isset($value['type']) && $value['type'] === AttributeTypes::ATTRIBUTE_JSON_TYPE) {
			$this->value = $value;
			return true;
		}
		return false;
	}
	
	/**
	 * @inheritDoc
	 * @return string
	 */
	public function forDatabase()
	{
		return json_encode($this->value);
	}
	
	/**
	 * @return array
	 */
	public function forClient()
	{
		return $this->value;
	}
	
}