<?php
/*
 */

namespace Bogo\Yii\DynSchema\Component\Widget;

/**
 * Raw value view widget.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class ViewRawValue extends Base
{
	/**
	 * @inheritdoc
	 */
	public function render($value = null)
	{
		return $value;
	}
}
