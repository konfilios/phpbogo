<?php
/*
 */

namespace Bogo\Yii\DynSchema\DynString;

use \Bogo\DynSchema\IDatatype;
use \CHtml;

/**
 * Call event.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Ui
{
	public function inputWidget(IDatatype $datatype, $name, $value, $params = array())
	{
		return CHtml::textField($name, $value, $params);
	}
}
