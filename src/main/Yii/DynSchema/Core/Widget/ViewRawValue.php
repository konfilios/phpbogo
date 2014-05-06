<?php
/*
 */

namespace Bogo\Yii\DynSchema\Core\Widget;

/**
 * Raw value view widget.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class ViewRawValue extends \Bogo\Yii\DynSchema\Core\Widget
{
	/**
	 * @inheritdoc
	 */
	public function render($value = null)
	{
		return $value;
	}
}
