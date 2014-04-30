<?php
/*
 */

namespace Bogo\DynSchema;

/**
 * DynSchema widget component.
 * 
 * Presentation widget for dynamic attributes.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IWidget
{
	/**
	 * Identifier of widget type.
	 *
	 * @return string
	 */
	public function getId();

	/**
	 * Render widget with given value for given scenario.
	 * 
	 * @param mixed $value
	 */
	public function render($value = null);
}
