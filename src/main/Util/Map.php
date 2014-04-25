<?php
namespace Bogo\Util;

/**
 * Description of Map
 *
 * @author software6
 */
class Map {
	static function matchField($field, $value) {
		return function ($a) use ($field, $value) {
			return $a->{$field} == $value;
		};
	}

	/**
	 * Returns value of first array entry for which $matchingCallback returns true.
	 *
	 * @param type $array
	 * @param type $matcher
	 * @return null
	 */
	static function findFirstValue(&$array, $matcher) {
		if (!empty($array)) {
			foreach ($array as $key=>$value) {
				if ($matcher($value, $key)) {
					return $value;
				}
			}
		}

		return null;
	}

	static function findFirstValueMatchingFieldValue($array, $field, $value) {
		return self::findFirstValue($array, self::matchField($field, $value));
	}
}
