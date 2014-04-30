<?php
/*
 */

namespace Bogo\DynSchema;

/**
 * Component factory.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IDynFactory
{
	/**
	 * Create a presentation widget according to given specifications.
	 *
	 * @param array $widgetSpec
	 * @return IWidget
	 */
	public function createWidget($widgetSpec);

	/**
	 * Create a validator according to given specifications.
	 *
	 * @param array $widgetSpec
	 * @return IValidator
	 */
	public function createValidator($validatorSpec);
}
