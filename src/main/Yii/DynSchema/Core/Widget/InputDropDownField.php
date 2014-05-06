<?php
/*
 */

namespace Bogo\Yii\DynSchema\Core\Widget;

use \CHtml;

/**
 * Drop down input widget.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class InputDropDownField extends \Bogo\Yii\DynSchema\Core\Widget
{
	/**
	 * @inheritdoc
	 */
	public function render($value = null)
	{
		return CHtml::dropDownList($this->name, $value,
				$this->attribute->getType()->getParam('enumOptions'),
				$this->htmlOptions);
	}
}
