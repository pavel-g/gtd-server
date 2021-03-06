<?php

namespace Gtd;

use \Gtd\Helper\Path;

class Util {
	
	public static $datetimeFormat = 'Y-m-d H:i:sP';
	
	/**
	 * Конвертация стиля CamelCase в underscore
	 *
	 * SampleString => sample_string
	 *
	 * @param string $str
	 * @return string
	 */
	public static function convertCamelCaseToUnderscore($str) {
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $str, $matches);
		$ret = $matches[0];
		foreach ($ret as &$match) {
			$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
		}
		return implode('_', $ret);
	}
	
	public static function recordKeysCamelCaseToUnderscore($record) {
		$res = [];
		foreach ($record as $key => $value) {
			$newKey = static::convertCamelCaseToUnderscore($key);
			$res[$newKey] = $value;
		}
		return $res;
	}

	/**
	 * @param array[] $store массив ассоциативных массивов
	 * @return array[] массив ассоциативных массивов
	 */
	public static function storeKeysCamelCaseToUnderscore($store) {
		$res = [];
		foreach ( $store as $record ) {
			$res[] = static::recordKeysCamelCaseToUnderscore($record);
		}
		return $res;
	}
	
	public static function checkNumericOrNull($value) {
		return ((boolean) (($value === null) || is_numeric($value)));
	}
	
	public static function checkStringOrNull($value) {
		return ((boolean) (
			$value === null ||
			is_string($value)
		));
	}
	
	public static function stringToDate($str) {
		if (!is_string($str)) {
			return null;
		}
		$date = \DateTime::createFromFormat(self::$datetimeFormat, $str);
		if ($date === false) {
			return null;
		}
		return $date;
	}
	
	public static function dateToString($date) {
		if ( !($date instanceof \DateTime) ) {
			return null;
		}
		return $date->format(self::$datetimeFormat);
	}
	
	public static function createPathFromTask($task) {
		$path = $task->getPath();
		$id = $task->getId();
		if ($path === '') {
			return new Path($id);
		} else {
			return new Path($path . '/' . $id);
		}
	}
	
}