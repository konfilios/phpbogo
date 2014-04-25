<?php
/*
 */

namespace Bogo\Yii\DynSchema;

/**
 * Attribute.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Attribute
{
	/**
	 *
	 * @var integer
	 */
	public $id;

	/**
	 *
	 * @var string
	 */
	public $label;

	/**
	 *
	 * @var Datatype
	 */
	public $datatype;

	/**
	 *
	 * @var mixed
	 */
	public $defaultValue = null;
}
