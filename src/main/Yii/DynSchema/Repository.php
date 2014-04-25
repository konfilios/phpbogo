<?php
/*
 */

namespace Bogo\Yii\DynSchema;

use \CApplicationComponent;

/**
 * Call event.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Repository extends CApplicationComponent
{
	public $classMap = array(
		'string' => array(
			'persistorClass' => '\Bogo\Yii\DynSchema\DynString\ActivePersistor',
			'uiClass' => '\Bogo\Yii\DynSchema\DynString\Ui',
			'validatorClass' => '\Bogo\Yii\DynSchema\DynString\Validator',
			'datatypeClass' => '\Bogo\Yii\DynSchema\DynString\Datatype',
		),
		'enum' => array(
			'persistorClass' => '\Bogo\Yii\DynSchema\DynEnum\ActivePersistor',
			'uiClass' => '\Bogo\Yii\DynSchema\DynEnum\Ui',
			'validatorClass' => '\Bogo\Yii\DynSchema\DynEnum\Validator',
			'datatypeClass' => '\Bogo\Yii\DynSchema\DynEnum\Datatype',
		),
		'boolean' => array(
			'persistorClass' => '\Bogo\Yii\DynSchema\DynBoolean\ActivePersistor',
			'uiClass' => '\Bogo\Yii\DynSchema\DynBoolean\Ui',
			'validatorClass' => '\Bogo\Yii\DynSchema\DynBoolean\Validator',
			'datatypeClass' => '\Bogo\Yii\DynSchema\DynBoolean\Datatype',
		),
	);

	/**
	 * @return Datatype
	 */
	public function createDatatype($id, $params)
	{
		$className = $this->classMap[$id]['datatypeClass'];
		return new $className(array('id' => $id) + $params);
	}

	/**
	 * @return Datatype
	 */
	public function createUi($id)
	{
		$className = $this->classMap[$id]['uiClass'];
		return new $className();
	}
}
