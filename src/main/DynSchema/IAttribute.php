<?php
/*
 */

namespace Bogo\DynSchema;

/**
 * Attribute model.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IAttribute
{
	/**
	 * Unique identifier.
	 *
	 * @return string|integer
	 */
	public function getId();

	/**
	 * Default value.
	 *
	 * @return mixed
	 */
	public function getDefaultValue();

	/**
	 * Datatype id for individual values.
	 *
	 * @return string
	 */
	public function getValueDatatypeId();

	/**
	 * Collection type id of values.
	 */
	public function getValueCollectionTypeId();

	/**
	 * Validation scenaria.
	 *
	 * @return array
	 */
	public function getValidationScenaria();

	/**
	 * Presentation scenaria.
	 *
	 * @return array
	 */
	public function getPresentationScenaria();
}
