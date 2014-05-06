<?php
/*
 */

namespace Bogo\DynSchema\Core\AttributeType;

use \Bogo\DynSchema\Core\IAttributeType;

/**
 * Collections of elementary fields.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface ICollection extends IAttributeType
{
	const ID_NONE = 'None';
	const ID_LIST = 'List';

	/**
	 * Element type.
	 *
	 * @return IElementary
	 */
	public function getElementType();
}
