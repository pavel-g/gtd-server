<?php

namespace Gtd\Cli;


use Gtd\Propel\AttributeTypes;
use Gtd\Propel\AttributeTypesQuery;
use Gtd\Propel\TypeGroups;
use Gtd\Propel\TypeGroupsQuery;
use Gtd\Propel\Types;
use Gtd\Propel\TypesQuery;

class Install
{
	
	const ATTRIBUTE_VALUE_TYPE = 'ATTRIBUTE_VALUE_TYPE';
	
	const ATTRIBUTE_STRING_TYPE = 'ATTRIBUTE_STRING_TYPE';
	const ATTRIBUTE_INTEGER_TYPE = 'ATTRIBUTE_INTEGER_TYPE';
	const ATTRIBUTE_FLOAT_TYPE = 'ATTRIBUTE_FLOAT_TYPE';
	const ATTRIBUTE_BOOLEAN_TYPE = 'ATTRIBUTE_BOOLEAN_TYPE';
	const ATTRIBUTE_JSON_TYPE = 'ATTRIBUTE_JSON_TYPE';
	
	const ATTRIBUTE_HASHTAGS = 'ATTRIBUTE_HASHTAGS';
	const ATTRIBUTE_REPEAT_RULE = 'ATTRIBUTE_REPEAT_RULE';
	
	public function install()
	{
		echo "Installation..." . PHP_EOL;
		
		echo "Prepare attribute types...";
		$this->prepareConstants();
		echo "DONE" . PHP_EOL;
	}
	
	public function prepareConstants()
	{
		$attributeValueType = TypeGroupsQuery::create()->findOneByCode(self::ATTRIBUTE_VALUE_TYPE);
		if (!$attributeValueType) {
			$attributeValueType = new TypeGroups();
			$attributeValueType->setCode(self::ATTRIBUTE_VALUE_TYPE);
			$attributeValueType->setName('Attribtue value type');
			$attributeValueType->save();
		}
		
		$this->prepareType(self::ATTRIBUTE_STRING_TYPE);
		$this->prepareType(self::ATTRIBUTE_INTEGER_TYPE);
		$this->prepareType(self::ATTRIBUTE_FLOAT_TYPE);
		$this->prepareType(self::ATTRIBUTE_BOOLEAN_TYPE);
		$this->prepareType(self::ATTRIBUTE_JSON_TYPE);
		
		$this->prepareAttributeType(self::ATTRIBUTE_HASHTAGS, 'Hashtags', self::ATTRIBUTE_JSON_TYPE);
		$this->prepareAttributeType(self::ATTRIBUTE_REPEAT_RULE, 'Repeat rule', self::ATTRIBUTE_JSON_TYPE);
	}
	
	protected function prepareType($code)
	{
		$type = TypesQuery::create()->findOneByCode($code);
		if (!$type) {
			$type = new Types();
			$type->setGroupCode(self::ATTRIBUTE_VALUE_TYPE);
			$type->setCode($code);
			$type->save();
		}
	}
	
	protected function prepareAttributeType($code, $name, $type)
	{
		$t = AttributeTypesQuery::create()->findOneByCode($code);
		if (!$t) {
			$t = new AttributeTypes();
			$t->setCode($code);
			$t->setName($name);
			$t->setType($type);
			$t->save();
		}
	}
	
}