<?php
/*
 */

namespace Bogo\Yii\DynSchema\Component\Widget;

use \Bogo\DynSchema\IValueCollectionType;
use Bogo\Yii\DynSchema\Component\TEnumComponent;
use \CHtml;

/**
 * Drop down input widget.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class InputDropDown extends Base
{
	use TEnumComponent;

	/**
	 * @inheritdoc
	 */
	public function render($value = null)
	{
		$htmlOptions = $this->htmlOptions;

		if ($this->valueCollectionType != IValueCollectionType::ID_NONE) {
			$htmlOptions['multiple'] = 'multiple';
		}

		return CHtml::dropDownList($this->name, $value, $this->enumOptions, $htmlOptions);
	}
}
