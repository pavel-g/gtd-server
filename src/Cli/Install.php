<?php

namespace Gtd\Cli;


use Gtd\Propel\AttributeTypes;
use Gtd\Propel\AttributeTypesQuery;
use Gtd\Propel\TypeGroups;
use Gtd\Propel\TypeGroupsQuery;
use Gtd\Propel\Types;
use Gtd\Propel\TypesQuery;
use Gtd\AttributeTypes as TypeConsts;

class Install
{
	
	public function install()
	{
		echo "Installation..." . PHP_EOL;
		
		echo "Prepare attribute types...";
		$this->prepareConstants();
		echo "DONE" . PHP_EOL;
	}
	
	public function prepareConstants()
	{
		$attributeValueType = TypeGroupsQuery::create()->findOneByCode(TypeConsts::ATTRIBUTE_VALUE_TYPE);
		if (!$attributeValueType) {
			$attributeValueType = new TypeGroups();
			$attributeValueType->setCode(TypeConsts::ATTRIBUTE_VALUE_TYPE);
			$attributeValueType->setName('Attribtue value type');
			$attributeValueType->save();
		}
		
		$this->prepareType(TypeConsts::ATTRIBUTE_STRING_TYPE);
		$this->prepareType(TypeConsts::ATTRIBUTE_INTEGER_TYPE);
		$this->prepareType(TypeConsts::ATTRIBUTE_FLOAT_TYPE);
		$this->prepareType(TypeConsts::ATTRIBUTE_BOOLEAN_TYPE);
		$this->prepareType(TypeConsts::ATTRIBUTE_JSON_TYPE);
		$this->prepareType(TypeConsts::ATTRIBUTE_DATE_TYPE);
		$this->prepareType(TypeConsts::ATTRIBUTE_TIMESTAMP_TYPE);
		
		$this->prepareAttributeType(
			TypeConsts::ATTRIBUTE_HASHTAGS,
			'Hashtags',
			TypeConsts::ATTRIBUTE_JSON_TYPE
		);
		$this->prepareAttributeType(
			TypeConsts::ATTRIBUTE_REPEAT_RULE,
			'Repeat rule',
			TypeConsts::ATTRIBUTE_JSON_TYPE
		);
		$this->prepareAttributeType(
			TypeConsts::ATTRIBUTE_DUE_DATE,
			'Due date',
			TypeConsts::ATTRIBUTE_DATE_TYPE
		);
		$this->prepareAttributeType(
			TypeConsts::ATTRIBUTE_START_DATE,
			'Start date',
			TypeConsts::ATTRIBUTE_DATE_TYPE
		);
	}
	
	protected function prepareType($code)
	{
		$type = TypesQuery::create()->findOneByCode($code);
		if (!$type) {
			$type = new Types();
			$type->setGroupCode(TypeConsts::ATTRIBUTE_VALUE_TYPE);
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