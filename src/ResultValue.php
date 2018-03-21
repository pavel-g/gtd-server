<?php

namespace Gtd;

class ResultValue
{
	
	/**
	 * @var mixed
	 */
	protected $value = null;
	
	/**
	 * @var boolean|null
	 */
	protected $res = null;
	
	/**
	 * @param boolean $res
	 * @param mixed $value
	 */
	public function __construct($res, $value)
	{
		$this->res = $res;
		$this->value = $value;
	}
	
	/**
	 * @return boolean
	 */
	public function isSuccess()
	{
		return (boolean) $this->res;
	}
	
	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}
	
}