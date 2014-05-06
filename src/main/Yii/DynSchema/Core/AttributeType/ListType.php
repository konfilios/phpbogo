<?php
/*
 */

namespace Bogo\Yii\DynSchema\Core\AttributeType;

use \Bogo\Yii\DynSchema\Core\AttributeType;
use \Bogo\DynSchema\Core\AttributeType\ICollection;

/**
 * Attribute type.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class ListType extends AttributeType implements ICollection
{
	/**
	 * Element attribute type.
	 *
	 * @var ElementaryType
	 */
	private $elementType;

	/**
	 * @inheritdoc
	 */
	public function getElementType()
	{
		return $this->elementType;
	}

	/**
	 * @inheritdoc
	 */
	public function getSignatureString()
	{
		if ($this->elementType) {
			return $this->id.'<'.$this->elementType->getSignatureString().'>';
		} else {
			return $this->id;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getDefaultValue()
	{
		return array();
	}

	/**
	 * 
	 * @param ListType $listType
	 * @param ElementaryType $elementType
	 * @return type
	 */
	static public function createSignatureArray(ListType $listType, $elementType)
	{
		$signatureArray = array(
			'id' => $listType->getId()
		);

		if ($elementType) {
			$signatureArray['elementType'] = $elementType->getSignatureArray();
		}

		return $signatureArray;
	}

	public function getSignatureArray()
	{
		return self::createSignatureArray($this, $this->elementType);
	}

	/**
	 * @inheritdoc
	 */
	public function getSuperType()
	{
		if ($this->elementType) {
			$superSignatureArray = self::createSignatureArray($this, $this->elementType->getSuperType());
			return $this->engine->getAttributeType($superSignatureArray);
		} else {
			return null;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function __construct($engine, $signatureArray)
	{
//		echo "<pre>AttributeTypeList:\n";var_export($signatureArray);echo "</pre>";
		// Do standard construction
		parent::__construct($engine, $signatureArray);

		if (isset($signatureArray['elementType'])) {
			$this->elementType = $this->engine->getAttributeType($signatureArray['elementType']);
		}
//		echo "<pre>AttributeTypeList:\n";var_export($this->getSignatureArray());echo "</pre>";
	}
}
