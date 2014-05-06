<?php
/*
 */

namespace Bogo\DynSchema\Core;

/**
 * Constraint specification model.
 * 
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IConstraint
{
	const ID_NOT_EMPTY = 'NotEmpty';

	/**
	 * Unique identifier of constraint.
	 *
	 * @return integer|string
	 */
	public function getId();

	/**
	 * Free parameters.
	 * 
	 * @return array|null
	 */
	public function getParams();

	/**
	 * Get errors for given value.
	 * 
	 * @param mixed $value
	 * @return string[]
	 */
	public function getViolations($value);
}
