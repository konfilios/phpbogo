<?php
/*
 */

namespace Bogo\Yii\DynSchema\DynEnum;

/**
 * DynEnum datatype.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Datatype extends \Bogo\Yii\DynSchema\Datatype
{
	private $_enumOptions = array();

	public function getEnumOptions()
	{
		return $this->_enumOptions;
	}

	public function setEnumOptions($enumOptions)
	{
		$this->_enumOptions = $enumOptions;
	}
}
