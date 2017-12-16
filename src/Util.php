<?php

namespace Gtd;

class Util {
	
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

	/**
	 * @param array[] $store массив ассоциативных массивов
	 * @return array[] массив ассоциативных массивов
	 */
	public static function storeKeysCamelCaseToUnderscore($store) {
		$res = [];
		foreach ( $store as $rec0 ) {
			$rec1 = [];
			foreach ( $rec0 as $key0 => $value0 ) {
				$key1 = static::convertCamelCaseToUnderscore($key0);
				$rec1[$key1] = $value0;
			}
			$res[] = $rec1;
		}
		return $res;
	}	
	
}