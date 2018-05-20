<?php

namespace Gtd;

class RepeatTypes
{
	
	const AUTO_REPEAT = 'AUTO_REPEAT';
	const BY_HAND_REPEAT = 'BY_HAND_REPEAT';
	
	/**
	 * List of all types
	 *
	 * @return array
	 */
	public static function getRepeatTypes()
	{
		return [
			self::AUTO_REPEAT,
			self::BY_HAND_REPEAT
		];
	}
	
}