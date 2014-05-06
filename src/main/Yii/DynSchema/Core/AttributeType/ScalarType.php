<?php
/*
 */

namespace Bogo\Yii\DynSchema\Core\AttributeType;

use \Bogo\DynSchema\Core\AttributeType\IScalar;

/**
 * Attribute instance.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class ScalarType extends ElementaryType implements IScalar
{
	/**
	 * Attribute type.
	 *
	 * @var ScalarType
	 */
	private $superType;

	private $valueDatatypeId;

	private $defaultValue;

	/**
	 * @inheritdoc
	 */
	public function getSignatureString()
	{
		return $this->id;
	}

	/**
	 * @inheritdoc
	 */
	public function getSignatureArray()
	{
		return array(
			'id' => $this->id
		);
	}

	/**
	 * @inheritdoc
	 */
	public function getValueDatatypeId()
	{
		return $this->valueDatatypeId;
	}

	/**
	 * @inheritdoc
	 */
	public function getDefaultValue()
	{
		return $this->defaultValue;
	}

	/**
	 * @inheritdoc
	 */
	public function getSuperType()
	{
		return $this->superType;
	}

	/**
	 * @inheritdoc
	 */
	public function __construct($engine, $signatureArray)
	{
		// Do standard construction
		parent::__construct($engine, $signatureArray);
	}

	/**
	 * @inheritdoc
	 */
	public function init($spec)
	{
		parent::init($spec);

		// Initialize value datatype
		if (isset($spec['valueDatatype'])) {
			$this->valueDatatypeId = $spec['valueDatatype']['id'];
		}

		// Initialize default value
		if (isset($spec['defaultValue'])) {
			$this->defaultValue = $spec['defaultValue'];
		}

		if (isset($spec['superType'])) {
			// Create instance of parent attribute type
			$this->superType = $this->engine->getAttributeType($spec['superType']);

			if (empty($this->valueDatatypeId)) {
				$this->valueDatatypeId = $this->superType->getValueDatatypeId();
			}

			if (!isset($spec['defaultValue'])) {
				$this->defaultValue = $this->superType->getDefaultValue();
			}
		}
	}
}
