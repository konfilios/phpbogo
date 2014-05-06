<?php
/*
 */

namespace Bogo\Yii\DynSchema\Service;

use \Bogo\DynSchema\Service\IAttributeSpecRepository;
use \CActiveRecordBehavior;
use \CActiveRecord;

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
abstract class AttributeSpecRepositoryBehavior extends CActiveRecordBehavior implements IAttributeSpecRepository
{
	/**
	 * Construct an attribute spec from passed model.
	 * 
	 * @param CActiveRecord Source active record model.
	 * @return array
	 */
	abstract public function getSpecFromModel(CActiveRecord $model);

	/**
	 * Get spec of current model.
	 *
	 * @return array
	 */
	public function getSpec()
	{
		return $this->getSpecFromModel($this->owner);
	}

	/**
	 * @inheritdoc
	 */
	public function getAttributeSpecsByIds($ids)
	{
		$specs = array();
		foreach ($this->owner->findAllByPk($ids) as $model) {
			$specs = $this->getSpecFromModel($model);
		}
		return $specs;
	}

	/**
	 * @inheritdoc
	 */
	public function getAttributeSpecById($id)
	{
		$model = $this->owner->findByPk($id);

		if ($model) {
			return $this->getSpecFromModel($model);
		} else {
			return null;
		}
	}
}
