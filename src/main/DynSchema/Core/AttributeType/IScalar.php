<?php
/*
 */

namespace Bogo\DynSchema\Core\AttributeType;

/**
 * Single-field attributes.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IScalar extends IElementary
{
	const ID_INTEGER = 'Integer';
	const ID_STRING = 'String';

	/**
	 * Value datatype id.
	 *
	 * @return mixed
	 */
	public function getValueDatatypeId();
}
