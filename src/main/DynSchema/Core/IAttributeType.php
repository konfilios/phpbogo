<?php
/*
 */

namespace Bogo\DynSchema\Core;

/**
 * Attribute type.
 * 
 * An attribute specification may be complete or partial.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IAttributeType
{
	/**
	 * Attribute unique identifier.
	 *
	 * @return string|integer
	 */
	public function getId();

	/**
	 * Post-construction initialization according to specs.
	 * 
	 * @param array $spec
	 */
	public function init($spec);

	/**
	 * Attribute type signature as string.
	 *
	 * @return string
	 */
	public function getSignatureString();

	/**
	 * Attribute type signature as array.
	 *
	 * @return array
	 */
	public function getSignatureArray();

	/**
	 * Default value.
	 * 
	 * @return mixed|null
	 */
	public function getDefaultValue();

	/**
	 * Attribute type.
	 *
	 * @var IAttributeType|null
	 */
	public function getSuperType();

	/**
	 * Retrieve widget for given attribute and scenario
	 * 
	 * @param IAttribute $attribute
	 * $param strnig $scenarioName
	 */
	public function getWidget(IAttribute $attribute, $scenarioName);

	/**
	 * Retrieve parameter by name.
	 * 
	 * @param string $paramName
	 * @return mixed
	 */
	public function getParam($paramName);
}
