<?php
/* 
 */

namespace Bogo\Types;

trait TTypedObject
{
	/**
	 * Attribute types.
	 *
	 * @return string[]
	 */
	public function attributeTypes()
	{
		return array();
	}

	/**
	 * Clones a source into a json model.
	 *
	 * @param array|object $source Source object or array to deeply copy attributes from.
	 * @return static Cloned Model subtype.
	 */
	static public function createOne($source)
	{
		if ($source === null) {
			return null;
		} else {
			return Engine::copyAttributesDeep($source, new static());
		}
	}

	/**
	 * Clones an array of sources into an array of json models.
	 *
	 * Original model array keys are preserved in final array.
	 *
	 * @param array $sources Array of source objects or arrays to deeply copy attributes from.
	 * @return static[] Cloned Model subtypes.
	 */
	static public function createMany(array $sources)
	{
		$jsonModels = array();
		foreach ($sources as $key=>$source) {
			// Copy attributes (include fromModel's dynamic properties)and add to final array
			$jsonModels[$key] = Engine::copyAttributesDeep($source, new static());
		}

		return $jsonModels;
	}
}