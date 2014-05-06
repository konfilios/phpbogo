<?php
/*
 */

namespace Bogo\Yii\DynSchema\Core\Widget;

use \Bogo\Yii\DynSchema\Core\TCollectionEnumComponent;
use \CHtml;

/**
 * Drop down input widget.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class InputMultiDropDownField extends \Bogo\Yii\DynSchema\Core\Widget
{
	use TCollectionEnumComponent;

	/**
	 * @inheritdoc
	 */
	public function render($value = null)
	{
		return CHtml::dropDownList($this->name, $value,
				$this->getAttributeEnumOptions(),
				$this->htmlOptions + array('multiple'=>'multiple'));
	}
}
