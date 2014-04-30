<?php
/*
 */

namespace Bogo\DynSchema;

/**
 * Base attribute functionality.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
trait TAttributeBase
{
	/**
	 * @inheritdoc
	 */
	public function getDefaultValue()
	{
		switch ($this->getValueCollectionTypeId()) {
		case IValueCollectionType::ID_NONE:
			// Null value
			return null;
		default:
			// Empty collection
			return array();
		}
	}
}
