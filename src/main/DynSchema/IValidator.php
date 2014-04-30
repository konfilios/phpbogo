<?php
/*
 */

namespace Bogo\DynSchema;

/**
 * Validator component.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IValidator
{
	/**
	 * Identifier of validator type.
	 *
	 * @return string
	 */
	public function getId();

	/**
	 * Get errors for given value.
	 * 
	 * @param mixed $value
	 * @return string[]
	 */
	public function getErrors($value);
}
