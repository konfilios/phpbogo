<?php
/*
 */

namespace Bogo\Yii\DynSchema\Service;

use \Bogo\DynSchema\Core\IAttribute;
use \Bogo\DynSchema\Service\IEngine;
use \Bogo\Yii\DynSchema\Core\Attribute;
use \Bogo\Yii\DynSchema\Core\AttributeType;
use \Bogo\Yii\DynSchema\Core\AttributeType\ListType;
use \Bogo\Yii\DynSchema\Core\AttributeType\ScalarType;

/**
 * Factory implementation.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Engine extends \CApplicationComponent implements IEngine
{
	/**
	 * List of static files to pre-load.
	 *
	 * @var string[]
	 */
	public $preloadSpecFiles = array();

	/**
	 * Attribute type specification map.
	 * 
	 * Attribute type instance labels are used as keys.
	 *
	 * @var type 
	 */
	private $_attributeTypes = array();

	/**
	 * Attribute specification/instance map.
	 * 
	 * Attribute ids are used as keys.
	 * If a map has been instantiated, then the attribute instance is the value.
	 * Otherwise, the attribute spec (array) is the value.
	 *
	 * @var string
	 */
	private $_attributes = array();

	/**
	 * Register an attribute type's specification.
	 *
	 * @param mixed $label
	 * @param array $spec
	 */
	private function registerAttributeTypeSpec($spec)
	{
		// Extract signature
		$signatureArray = $spec['signature'];
		unset($spec['signature']);

		$attributeType = $this->getAttributeType($signatureArray);
		$attributeType->init($spec);
	}

	/**
	 * Register an attribute's specification.
	 *
	 * @param mixed $id
	 * @param array $spec
	 */
	private function registerAttributeSpec($spec)
	{
		$this->_attributes[$spec['id']] = $spec;
	}

	/**
	 * @inheritdoc
	 */
	public function registerSpec($spec)
	{
		if (isset($spec['attributeTypes'])) {
			foreach ($spec['attributeTypes'] as $attrTypeSpec) {
				$this->registerAttributeTypeSpec($attrTypeSpec);
			}
		}

		if (isset($spec['attributes'])) {
			foreach ($spec['attributes'] as $attrSpec) {
				$this->registerAttributeSpec($attrSpec);
			}
		}
	}

	/**
	 * Load a specification file.
	 *
	 * @param string $filePath
	 */
	public function loadSpecFile($filePath)
	{
		$spec = json_decode(file_get_contents($filePath), true);
		$this->registerSpec($spec);
	}

	/**
	 * Create an attribute type.
	 *
	 * @param array $signatureArray
	 * @param array $spec
	 * @return AttributeType
	 */
	private function createAttributeType($signatureArray)
	{
		if ($signatureArray['id'] == 'List') {
			return new ListType($this, $signatureArray);
		} else {
			return new ScalarType($this, $signatureArray);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getAttributeType($signatureArray)
	{
		$signatureString = serialize($signatureArray);

		if (!isset($this->_attributeTypes[$signatureString])) {
			$this->_attributeTypes[$signatureString] = $this->createAttributeType($signatureArray);
		}

		return $this->_attributeTypes[$signatureString];
	}

	/**
	 * Create attribute instance.
	 *
	 * @param array $spec
	 * @return \Bogo\Yii\DynSchema\Core\Attribute
	 */
	private function createAttribute($spec)
	{
		return new Attribute($this, $spec);
	}

	/**
	 * Return attribute instance by id.
	 *
	 * @param string|integer $attrId
	 * @return \Bogo\Yii\DynSchema\Core\Attribute
	 * @throws Exception
	 */
	public function getAttribute($attrId)
	{
		// Check if attribute has been registered
		if (!isset($this->_attributes[$attrId])) {
			throw new Exception('Unregistered attribute "'.$attrId.'"');
		}

		if (is_array($this->_attributes[$attrId])) {
			// Retrieve spec
			$spec = $this->_attributes[$attrId];

			// Instantiate attribute from spec
			$attr = $this->createAttribute($spec);

			// Replace spec with instance
			$this->_attributes[$attrId] = $attr;
		}

		// Return instance
		return $this->_attributes[$attrId];
	}


	/**
	 * @inheritdoc
	 */
	public function createConstraint(IAttribute $attribute, $spec)
	{
		$className = '\Bogo\Yii\DynSchema\Core\Constraint\\'.$spec['id'];

		return new $className($this, $attribute, $spec);
	}

	/**
	 * @inheritdoc
	 */
	public function createWidget(IAttribute $attribute, $spec)
	{
		$className = '\Bogo\Yii\DynSchema\Core\Widget\\'.$spec['id'];

		return new $className($this, $attribute, $spec);
	}

	public function init()
	{
		parent::init();

		// Pre-load spec files
		foreach ($this->preloadSpecFiles as $specFile) {
			$this->loadSpecFile($specFile);
		}
	}
}
