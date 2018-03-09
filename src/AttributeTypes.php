<?php

namespace Gtd;

class AttributeTypes
{
	
	const ATTRIBUTE_VALUE_TYPE = 'ATTRIBUTE_VALUE_TYPE';
	
	const ATTRIBUTE_STRING_TYPE = 'ATTRIBUTE_STRING_TYPE';
	const ATTRIBUTE_INTEGER_TYPE = 'ATTRIBUTE_INTEGER_TYPE';
	const ATTRIBUTE_FLOAT_TYPE = 'ATTRIBUTE_FLOAT_TYPE';
	const ATTRIBUTE_BOOLEAN_TYPE = 'ATTRIBUTE_BOOLEAN_TYPE';
	const ATTRIBUTE_JSON_TYPE = 'ATTRIBUTE_JSON_TYPE';
	
	const ATTRIBUTE_HASHTAGS = 'ATTRIBUTE_HASHTAGS';
	const ATTRIBUTE_REPEAT_RULE = 'ATTRIBUTE_REPEAT_RULE';
	
	/**
	 * @param string $type
	 * @return boolean
	 */
	public static function isAttributeType($type)
	{
		$types = [
			self::ATTRIBUTE_HASHTAGS,
			self::ATTRIBUTE_REPEAT_RULE
		];
		return in_array($type, $types);
	}
	
	/**
	 * @param string $valueType
	 * @return string|null
	 */
	public static function getValueField($valueType)
	{
		switch ($valueType) {
			case self::ATTRIBUTE_BOOLEAN_TYPE:
				return 'boolean_value';
			case self::ATTRIBUTE_FLOAT_TYPE:
				return 'float_value';
			case self::ATTRIBUTE_INTEGER_TYPE:
				return 'integer_value';
			case self::ATTRIBUTE_JSON_TYPE:
			case self::ATTRIBUTE_STRING_TYPE:
				return 'string_value';
			default:
				return null;
		}
	}
	
}