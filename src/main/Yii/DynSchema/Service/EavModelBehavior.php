<?php
/*
 */

namespace Bogo\Yii\DynSchema\Service;

use \Bogo\DynSchema\Core\IAttribute;
use \Bogo\DynSchema\Core\AttributeType\ICollection;
use \Bogo\DynSchema\Core\AttributeType\IScalar;
use \Bogo\DynSchema\Service\ISpecRepository;
use \Bogo\DynSchema\Service\IEngine;
use \CActiveRecordBehavior;
use \CActiveRecord;

/**
 * Entity-Attribute-Value behavior.
 * 
 * Aggregates a list of dynamic bag of values.
 *
 * @property CActiveRecord $owner 
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class EavModelBehavior extends CActiveRecordBehavior
{
	/**
	 * Spec repository.
	 *
	 * @var ISpecRepository
	 */
	public $specRepository;

	/**
	 * Engine.
	 *
	 * @var IEngine
	 */
	public $engine;

	/**
	 * Value storage table name per data type.
	 *
	 * @var string[]
	 */
	public $valueTables;

	/**
	 * Value map.
	 * 
	 * Attribute id is the key.
	 *
	 * @var array 
	 */
	public $values = array();

	/**
	 * Set new value corresponding to given attrId.
	 *
	 * @param integer $attrId
	 * @param mixed $value
	 */
	public function setValue($attrId, $value)
	{
		$this->values[$attrId] = $value;
	}

	/**
	 * @param array $attrIds
	 * @return IAttribute[]
	 */
	public function registerSpecForAttributeIds($attrIds)
	{
		$spec = $this->specRepository->getSchemaSpecByAttributeIds($attrIds);
		$this->engine->registerSpec($spec);
	}

	/**
	 * Get value corresponding to given attrId.
	 * 
	 * If no value is set for this attrId, the corresponding attributes
	 * default value is used.
	 * 
	 * @param type $attrId
	 * @return type
	 */
	public function getValue($attrId)
	{
		return $this->values[$attrId];
	}

	/**
	 * Insert single value of given attribute.
	 *
	 * @param \Bogo\DynSchema\Core\IAttribute $attr
	 * @param mixed $value
	 */
	protected function insertValue($datatypeId, $attrId, $value)
	{
		// Get table in which we store values of this datatype
		$valueTable = $this->valueTables[$datatypeId];

		// Insert value
		$this->owner->dbConnection->createCommand()->insert($valueTable, array(
			'ownerId' => $this->owner->id,
			'attrId' => $attrId,
			'value' => $value
		));
	}

	/**
	 * Persist value of given attribute.
	 *
	 * @param \Bogo\DynSchema\Core\IAttribute $attr
	 * @param mixed $value
	 */
	protected function saveValue(IAttribute $attr, $value)
	{
		$attrType = $attr->getType();

		if ($attrType instanceof ICollection) {
			// Array of values
			foreach ($value as $valueElement) {
				$this->insertValue($attrType->getElementType()->getValueDatatypeId(), $attr->getId(), $valueElement);
			}

		} else if ($attrType instanceof IScalar) {
			// Single value
			$this->insertValue($attrType->getValueDatatypeId(), $attr->getId(), $value);
		} else {
			throw new \Exception("Unhandled datatype ".$attrType->getSignatureString());
		}
	}

	/**
	 * Delete all existing values of owner from all value storage tables.
	 */
	protected function deleteValues()
	{
		foreach ($this->valueTables as $valueTable) {
			$this->owner->dbConnection->createCommand()->delete($valueTable,
					'ownerId = :ownerId', array(':ownerId' => $this->owner->id));
		}
	}

	/**
	 * Persist values.
	 */
	public function saveValues()
	{
		$this->deleteValues();

		// Get attribute ids we're interested in
		$attrIds = array_keys($this->values);
		$this->registerSpecForAttributeIds($attrIds);

		// Go through all attributes
		foreach ($this->values as $attrId=>$value) {
			$attr = $this->engine->getAttribute($attrId);

			$this->saveValue($attr, $value);
		}
	}

	/**
	 * Read values from persistence.
	 */
	public function readValues()
	{
		$selectedValues = array();

		// Go through all value storage tables
		foreach ($this->valueTables as $valueTable) {

			// Select values associated with our owner
			$rows = $this->owner->dbConnection->createCommand()
				->select('attrId, value')
				->from($valueTable)
				->where('ownerId = :ownerId', array(':ownerId' => $this->owner->id))
				->queryAll();

			foreach ($rows as $row) {
				$attrId = $row['attrId'];
				$value = $row['value'];

				// Store selected values as if all attributes were collections
				$selectedValues[$attrId][] = $value;
			}
		}

		// Get attribute ids we're interested in
		$attrIds = array_keys($selectedValues);
		$this->registerSpecForAttributeIds($attrIds);

		// Go through all attributes to tell which are collections and which not
		$this->values = array();
		foreach ($selectedValues as $attrId=>$selectedValueAsArray) {
			$attr = $this->engine->getAttribute($attrId);

			if ($attr->getType() instanceof ICollection) {
				// Not a collection. Use first element of selected value array
				$this->values[$attrId] = $selectedValueAsArray[0];
			} else {
				// Collection. Use whole selected value array
				$this->values[$attrId] = $selectedValueAsArray;
			}
		}
	}
}
