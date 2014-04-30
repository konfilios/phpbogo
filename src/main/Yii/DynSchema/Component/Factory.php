<?php
/*
 */

namespace Bogo\Yii\DynSchema\Component;

use \Bogo\DynSchema\IDynFactory;
use \CApplicationComponent;

/**
 * Factory implementation.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Factory extends CApplicationComponent implements IDynFactory
{
	/**
	 * Generic factory method.
	 * 
	 * Creates Widgets and Validators.
	 *
	 * @param string $componentType
	 * @param array $componentSpec
	 * @return object
	 */
	private function createComponent($componentType, $componentSpec)
	{
		// Figure out class
		$componentClassName = '\Bogo\Yii\DynSchema\Component\\'.$componentType.'\\'.$componentSpec['id'];
		unset($componentSpec['id']);

		// Instantiate
		$componentInstance = new $componentClassName();

		// Configure
		foreach ($componentSpec as $field=>$value) {
			$componentInstance->{$field} = $value;
		}

		return $componentInstance;
	}

	/**
	 * @inheritdoc
	 */
	public function createValidator($validatorSpec)
	{
		return $this->createComponent('Validator', $validatorSpec);
	}

	/**
	 * @inheritdoc
	 */
	public function createWidget($widgetSpec)
	{
		return $this->createComponent('Widget', $widgetSpec);
	}
}
