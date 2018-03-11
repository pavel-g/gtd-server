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
	const ATTRIBUTE_DATE_TYPE = 'ATTRIBUTE_DATE_TYPE';
	const ATTRIBUTE_TIMESTAMP_TYPE = 'ATTRIBUTE_TIMESTAMP_TYPE';
	
	const ATTRIBUTE_HASHTAGS = 'ATTRIBUTE_HASHTAGS';
	const ATTRIBUTE_REPEAT_RULE = 'ATTRIBUTE_REPEAT_RULE';
	const ATTRIBUTE_DUE_DATE = 'ATTRIBUTE_DUE_DATE';
	const ATTRIBUTE_START_DATE = 'ATTRIBUTE_START_DATE';
	
	/**
	 * @param string $type
	 * @return boolean
	 */
	public static function isAttributeType($type)
	{
		$types = [
			self::ATTRIBUTE_HASHTAGS,
			self::ATTRIBUTE_REPEAT_RULE,
			self::ATTRIBUTE_DUE_DATE,
			self::ATTRIBUTE_START_DATE
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
			case self::ATTRIBUTE_DATE_TYPE:
				return 'date_value';
			case self::ATTRIBUTE_TIMESTAMP_TYPE:
				return 'timestamp_value';
			default:
				return null;
		}
	}
	
	/**
	 * @param string $type
	 * @return string|null
	 */
	public static function getValueFieldByAttributeType($type)
	{
		switch ($type) {
			case self::ATTRIBUTE_HASHTAGS:
			case self::ATTRIBUTE_REPEAT_RULE;
				return 'string_value';
			case self::ATTRIBUTE_DUE_DATE:
			case self::ATTRIBUTE_START_DATE:
				return 'date_value';
			default:
				return null;
		}
	}
	
}