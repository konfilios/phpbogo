<?php
/*
 */

namespace Bogo\Yii\DynSchema\Component\Widget;

use \Bogo\DynSchema\IValueCollectionType;
use \CHtml;

/**
 * Text field input widget.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class InputTextField extends Base
{
	/**
	 * @inheritdoc
	 */
	public function render($value = null)
	{
		// Validate supported vollection types
		$this->validateValueCollectionType(array(
			IValueCollectionType::ID_NONE
		));

		return CHtml::textField($this->name, $value, $this->htmlOptions);
	}
}
