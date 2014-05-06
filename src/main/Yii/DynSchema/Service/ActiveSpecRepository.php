<?php
/*
 */

namespace Bogo\Yii\DynSchema\Service;

use \Bogo\DynSchema\Service\ISpecRepository;
use \CActiveRecordBehavior;
use \CActiveRecord;
use \CApplicationComponent;

/**
 * Attribute spec repository behavior.
 * 
 * Aggregates a list of dynamic bag of values.
 *
 * @property CActiveRecord $owner 
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
abstract class ActiveSpecRepository
	extends CApplicationComponent
	implements ISpecRepository
{
	/**
	 * Construct an attribute spec from passed model.
	 * 
	 * If model does not yield an attribute type, then return null.
	 *
	 * @param CActiveRecord Source active record model.
	 * @return array|null
	 */
	abstract public function getAttributeSpecFromModel(CActiveRecord $model);

	/**
	 * Construct an attribute type spec from passed model.
	 * 
	 * If model does not yield an attribute type, then return null.
	 * 
	 * @param CActiveRecord Source active record model.
	 * @return array|null
	 */
	abstract public function getAttributeTypeSpecFromModel(CActiveRecord $model);

	/**
	 * 
	 * @param CActiveRecord[] $models
	 * @return array
	 */
	public function getSchemaSpecFromModels($models)
	{
		$spec = array(
			'attributeTypes' => array(),
			'attributes' => array(),
		);

		foreach ($models as $model) { /* @var $productAttribute ProductAttribute */
			// See if model yields any attribute type
			$attributeTypeSpec = $this->getAttributeTypeSpecFromModel($model);

			if ($attributeTypeSpec) {
				$spec['attributeTypes'][] = $attributeTypeSpec;
			}

			// See if model yields any attribute
			$attributeSpec = $this->getAttributeSpecFromModel($model);

			if ($attributeSpec) {
				$spec['attributes'][] = $attributeSpec;
			}
		}
		return $spec;
	}
}
