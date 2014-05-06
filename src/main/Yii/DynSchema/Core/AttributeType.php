<?php
/*
 */

namespace Bogo\Yii\DynSchema\Core;

use \Bogo\Yii\DynSchema\Service\Service;
use \Bogo\DynSchema\Core\IAttributeType;
use \Bogo\DynSchema\Core\IAttribute;

/**
 * Attribute type.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
abstract class AttributeType implements IAttributeType
{
	/**
	 * Owner service.
	 *
	 * @var Service 
	 */
	protected $service;

	/**
	 * Attribute type id.
	 *
	 * @var string|integer
	 */
	protected $id;

	private $scenarioSpecs = array();

	private $featureSpecs = array();

	private $params = array();

	/**
	 * Construct according to signature.
	 *
	 * @param Service $service
	 * @param array $signatureArray
	 */
	public function __construct($service, $signatureArray)
	{
		$this->service = $service;

		$this->id = $signatureArray['id'];
	}

	/**
	 * @inheritdoc
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Initialize according to specification.
	 *
	 * @param array $spec
	 */
	public function init($spec)
	{
		// Evaluate feature specs
		if (isset($spec['features'])) {
			$this->featureSpecs = $spec['features'];
		}

		// Evaluate scenario specs
		if (isset($spec['scenaria'])) {
			$this->scenarioSpecs = $spec['scenaria'];
		}

		// Evaluate scenario specs
		if (isset($spec['params'])) {
			$this->params = $spec['params'];
		}
	}

	public function getWidget(IAttribute $attribute, $scenarioName)
	{
//		var_export($this->getSignatureString());
		// Try to fulfil request with our specs
		if (isset($this->scenarioSpecs[$scenarioName])) {
			return $this->service->createWidget($attribute, $this->scenarioSpecs[$scenarioName]['widget']);
		}
		
		// Delegate to super type
		$superType = $this->getSuperType();

		if ($superType) {
			return $superType->getWidget($attribute, $scenarioName);
		} else {
			return null;
		}
	}

	public function getScenarioSpecs()
	{
		return $this->scenarioSpecs;
	}

	public function getParam($paramName)
	{
		if (isset($this->params[$paramName])) {
			return $this->params[$paramName];
		}

		// Delegate to super type
		$superType = $this->getSuperType();

		if ($superType) {
			return $superType->getParam($paramName);
		} else {
			return null;
		}
	}
}
