<?php
/*
 */

namespace Bogo\Yii\DynSchema\Core;

use Bogo\Yii\DynSchema\Service\Service;
use Bogo\DynSchema\Core\IAttribute;

/**
 * Attribute instance.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Attribute implements IAttribute
{
	/**
	 * Id.
	 *
	 * @var string|integer
	 */
	private $id;

	/**
	 * Owner service.
	 *
	 * @var Service 
	 */
	private $service;

	/**
	 * Attribute type.
	 *
	 * @var AttributeType
	 */
	private $type;

	/**
	 * Default value.
	 *
	 * @var mixed
	 */
	private $defaultValue;

	/**
	 * Scenaria.
	 *
	 * @var array
	 */
	private $scenarioSpecs = array();

	public function __construct($service, $spec)
	{
		// Keep link to service
		$this->service = $service;

		// Copy id
		$this->id = $spec['id'];

		// Create instance of attribute type
		$this->type = $this->service->getAttributeType($spec['type']);

		// Initialize default value
		$this->defaultValue = isset($spec['defaultValue']) ? $spec['defaultValue'] : $this->type->getDefaultValue();

		// Evaluate scenario specs
		if (isset($spec['scenaria'])) {
			$this->scenarioSpecs = $spec['scenaria'];
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @inheritdoc
	 */
	public function getType()
	{
		return $this->type;
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
	public function getWidget($scenarioName)
	{
		if (isset($this->scenarioSpecs[$scenarioName])) {
			// We have specs for this scenario, create widget
			return $this->service->createWidget($this, $this->scenarioSpecs[$scenarioName]['widget']);
		} else {
			// Delegate to our datatype
			return $this->type->getWidget($this, $scenarioName);
		}
	}

	/**
	 * @inheritdoc
	 * @todo Implement constraints.
	 */
	public function getConstraints($scenarioName = null)
	{
		return array();
	}
}
