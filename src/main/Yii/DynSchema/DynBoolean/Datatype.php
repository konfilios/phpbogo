<?php
/*
 */

namespace Bogo\Yii\DynSchema\DynBoolean;

/**
 * DynBoolean datatype.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Datatype extends \Bogo\Yii\DynSchema\DynEnum\Datatype
{
	public function getEnumOptions()
	{
		return array(1=>'Ναι', 0=>'Όχι');
	}
}
