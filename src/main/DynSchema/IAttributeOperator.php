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
class IAttributeOperator
{
	const ID_EQUALS = 'Equals';

	const ID_LESS_THAN = 'LessThan';
	const ID_LESS_EQUAL = 'LessEqual';

	const ID_GREATER_THAN = 'GreaterThan';
	const ID_GREATER_EQUAL = 'GreaterEqual';

	const ID_BETWEEN = 'Between';
	const ID_IN = 'In';
}
