<?php
/*
 */

namespace Bogo\Yii\DynSchema\Component\Widget;

use Bogo\Yii\DynSchema\Component\TEnumComponent;

/**
 * Enumerated value view widget.
 * 
 * Value is enumerated, so instead of the value itself, we must display
 * the associated label.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class ViewEnumValue extends Base
{
	use TEnumComponent;

	/**
	 * @inheritdoc
	 */
	public function render($value = null)
	{
		if ($value === null) {
			return null;
		} else {
			return $this->enumOptions[$value];
		}
	}
}
