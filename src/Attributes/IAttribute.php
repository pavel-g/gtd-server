<?php

namespace Gtd\Attributes;

interface IAttribute
{
	
	/**
	 * @param mixed $value
	 * @return boolean
	 */
	public function setValue($value);
	
	/**
	 * @return mixed
	 */
	public function forDatabase();
	
	/**
	 * @return mixed
	 */
	public function forClient();
	
}