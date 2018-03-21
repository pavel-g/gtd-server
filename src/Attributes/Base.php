<?php

namespace Gtd\Attributes;

/**
 * Class for create attribute classes
 * 
 * Serialize and deserialize data between web-client and database
 */
abstract class Base implements IAttribute
{
	
	/**
	 * @var mixed
	 */
	protected $value = null;
	
	/**
	 * @inheritDoc
	 * @param mixed $value
	 * @return boolean
	 */
	abstract public function setValue($value);
	
	/**
	 * @inheritDoc
	 * @return mixed
	 */
	public function forDatabase()
	{
		return $this->value;
	}
	
	/**
	 * @inheritDoc
	 * @return mixed
	 */
	public function forClient()
	{
		return $this->value;
	}
	
}
