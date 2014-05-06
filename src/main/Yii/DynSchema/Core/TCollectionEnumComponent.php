<?php
/*
 */

namespace Bogo\Yii\DynSchema\Core;

use \Bogo\DynSchema\Core\AttributeType\ICollection;

/**
 * Enumerated values trait.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
trait TCollectionEnumComponent
{
	/**
	 * Retrieve attribute's enum options.
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function getAttributeEnumOptions()
	{
		$attributeType = $this->attribute->getType();

		if (!($attributeType instanceof ICollection)) {
			throw new \Exception('A collection attribute type is required.');
		}
		/* @var $attributeType ICollection */
		
		return $attributeType->getElementType()->getParam('enumOptions');
	}
}
