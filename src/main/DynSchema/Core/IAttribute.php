<?php
/*
 */

namespace Bogo\DynSchema\Core;

/**
 * Attribute.
 * 
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IAttribute
{
	/**
	 * Attribute unique identifier.
	 *
	 * @return string|integer
	 */
	public function getId();

	/**
	 * Attribute type.
	 * 
	 * @return IAttributeType
	 */
	public function getType();

	/**
	 * Default value.
	 * 
	 * Works with static overriding.
	 *
	 * @return mixed|null
	 */
	public function getDefaultValue();

	/**
	 * Get widget for given scenario.
	 *
	 * Works with dynamic overriding.
	 *
	 * @param string $scenarioName
	 * @return IWidget
	 */
	public function getWidget($scenarioName);

	/**
	 * Constraints for given scenario.
	 * 
	 * If scenario is not given, core constraints are returned.
	 *
	 * Works with dynamic overriding.
	 *
	 * @return IConstraint[]
	 */
	public function getConstraints($scenarioName = null);
}
