<?php
/*
 */

namespace Bogo\Yii\DynSchema\Core\Widget;

use \Bogo\DynSchema\IValueCollectionType;
use \CHtml;

/**
 * Text field input widget.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class InputTextField extends \Bogo\Yii\DynSchema\Core\Widget
{
	/**
	 * @inheritdoc
	 */
	public function render($value = null)
	{
		return CHtml::textField($this->name, $value, $this->htmlOptions);
	}
}
