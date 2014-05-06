<?php
/*
 */

namespace Bogo\DynSchema\Core;

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
	 * Unique identifier of widget type.
	 *
	 * @return integer|string
	 */
	public function getId();

	/**
	 * Render widget with given value for given scenario.
	 * 
	 * @param mixed $value
	 */
	public function render($value = null);
}
