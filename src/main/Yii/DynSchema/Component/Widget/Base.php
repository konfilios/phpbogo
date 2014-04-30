<?php
/*
 */

namespace Bogo\Yii\DynSchema\Component\Widget;

use \Bogo\Yii\DynSchema\Component\TBaseComponent;

/**
 * Base widget.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
abstract class Base implements Bogo\DynSchema\IWidget
{
	use TBaseComponent;

	/**
	 * Widget name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Master html entity html options.
	 *
	 * @var array
	 */
	public $htmlOptions = array();
}
