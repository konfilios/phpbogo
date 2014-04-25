<?php
/* 
 */

namespace Bogo\Types;

class Engine
{
	/**
	 * UTC datetime object.
	 *
	 * @var Datetime
	 */
	static private $utcDatetime;

	/**
	 * @return Datetime
	 */
	static private function getUtcDatetime()
	{
		if (self::$utcDatetime === null) {
			self::$utcDatetime = new \DateTime();
			self::$utcDatetime->setTimezone(new \DateTimeZone('UTC'));
		}

		return self::$utcDatetime;
	}

	/**
	 * Cast $sourceData into $targetType.
	 *
	 * @param string $targetType
	 * @param mixed $sourceData
	 * @return mixed
	 */
	static public function castFrom(&$sourceData, $targetType)
	{
		switch ($targetType) {
		case '':
		case 'mixed':
			return $sourceData;

		case 'integer':
			return intval($sourceData);

		case 'double':
		case 'float':
			return floatval($sourceData);

		case 'boolean':
			return boolval($sourceData);

		case 'utc8601datetime':
			return self::getUtcDatetime()->modify($sourceData)->format("Y-m-d\TH:i:s\Z");

		case 'utctimestamp':
			return self::getUtcDatetime()->modify($sourceData)->format("U");

		default:
			return self::copyAttributesDeep($sourceData, new $targetType());
		}
	}

	static public function parseType($typeString)
	{
		if ($typeString == 'array') {
			// Special case for "array" keyword
			$attrType = 'mixed';
			$isAttrTypeArray = true;

		} else {
			if (substr($typeString, -2) === '[]') {
				// Array type. Remove "[]"
				$isAttrTypeArray = true;
				$attrType = substr($typeString, 0, -2);
			} else {
				// Non-array type
				$attrType = $typeString;
				$isAttrTypeArray = false;
			}
		}

		return array($attrType, $isAttrTypeArray);
	}

	static public function copyAttributesDeep($source, $target)
	{
		if (is_array($source)) {
			$isSourceArray = true;
		} else if (is_object($source)) {
			$isSourceArray = false;
		} else {
			throw new \Exception(get_class($target).' instances cannot be initialized from a '
					.gettype($source).' source. An array or object is required');
		}

		// Our attribute types
		if (($target instanceof ITypedObject)) {
			$targetAttrTypes = $target->attributeTypes();
		} else {
			$targetAttrTypes = array();
		}
		$targetAttrNames = array_keys(get_object_vars($target));

		foreach ($targetAttrNames as $targetAttrName) {
			// Loop through our attributes

			if ($isSourceArray) {
				// Array key
				$targetAttrValue = isset($source[$targetAttrName]) ? $source[$targetAttrName] : null;
			} else {
				// Object property
				$targetAttrValue = isset($source->$targetAttrName) ? $source->$targetAttrName : null;
			}

			if ($targetAttrValue === null) {
				// Attribute not in source
				continue;
			}

			if (!isset($targetAttrTypes[$targetAttrName])) {
				// No typecast, copy-through
				$target->$targetAttrName = $targetAttrValue;
			} else {
				// Typecast required
				list($attrType, $isAttrTypeArray) = self::parseType($targetAttrTypes[$targetAttrName]);

				// Object or array of objects
				if ($isAttrTypeArray) {
					// Array of objects

					if (!is_array($targetAttrValue)) {
						// Make sure value is an array of objects
						throw new \Exception('Attribute '.$targetAttrName.' of '.get_class($target)
								.' must be an array of '.$attrType.' instances');
					}

					$arrayJsonModels = array();
					foreach ($targetAttrValue as $fromJsonAttributeElement) {
						// Loop through array of objects and create corresponding json models
						$arrayJsonModels[] = self::castFrom($fromJsonAttributeElement, $attrType);
					}
					$target->$targetAttrName = $arrayJsonModels;

				} else {
					// Simple object
					$target->$targetAttrName = self::castFrom($targetAttrValue, $attrType);
				}
			}
		}

		return $target;
	}

	/**
	 * Resolve an object to an array representation.
	 *
	 * @param mixed $inputObject
	 * @param boolean $doSuppressNulls If true, null properties are suppressed from result.
	 *
	 * @return mixed
	 */
	static public function attributesToArrayDeep($inputObject, $doSuppressNulls = false)
	{
		if (is_array($inputObject)) {
			//
			// Input object is an array, resolve its items
			//
			$objectArray = array();
			foreach ($inputObject as $key=>$inputObjectElement) {
				$objectArray[$key] = self::attributesToArrayDeep($inputObjectElement, $doSuppressNulls);
			}
			return $objectArray;

		} else if (is_object($inputObject)) {
			//
			// Input object is a class instance, handle specially
			//
			$inputAttributes = get_object_vars($inputObject);

			$outputAttributes = array();
			foreach ($inputAttributes as $attrName=>$attrValue) {
				if (is_array($attrValue) || is_object($attrValue)) {
					// Recurse
					$outputAttributes[$attrName] = self::attributesToArrayDeep($attrValue, $doSuppressNulls);

				} else if (!$doSuppressNulls || $attrValue !== null) {
					// Check suppression
					$outputAttributes[$attrName] = $attrValue;
				}
			}
			return $outputAttributes;
		} else {
			//
			// Input object is a scalar
			//
			return $inputObject;
		}
	}

	/**
	 * Performs a deep json-encode of input object's attributes.
	 *
	 * @param mixed $inputObject
	 * @param boolean $doSuppressNulls
	 * @return string
	 */
	static public function toJsonDeep($inputObject, $doSuppressNulls = false)
	{
		return json_encode(self::attributesToArrayDeep($inputObject, $doSuppressNulls));
	}
}
