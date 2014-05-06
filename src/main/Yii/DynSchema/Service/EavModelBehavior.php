<?php
/*
 */

namespace Bogo\Yii\DynSchema\Service;

use \Bogo\DynSchema\Service\IAttributeComponent;
use \Bogo\DynSchema\Service\IAttributeSpecRepository;
use \Bogo\DynSchema\Core\IValueCollectionType;
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
	 * Attribute repository.
	 *
	 * @var IAttributeSpecRepository
	 */
	public $attributeRepository;

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
	 * @param \Bogo\DynSchema\IAttributeComponent $attr
	 * @param mixed $value
	 */
	protected function insertValue(IAttributeComponent $attr, $value)
	{
		// Get storage data type id of attribute
		$datatypeId = $attr->getValueDatatypeId();

		// Get table in which we store values of this datatype
		$valueTable = $this->valueTables[$datatypeId];

		// Insert value
		$this->owner->dbConnection->createCommand()->insert($valueTable, array(
			'ownerId' => $this->owner->id,
			'attrId' => $attr->getId(),
			'value' => $value
		));
	}

	/**
	 * Persist value of given attribute.
	 *
	 * @param \Bogo\DynSchema\IAttributeComponent $attr
	 * @param mixed $value
	 */
	protected function saveValue(IAttributeComponent $attr, $value)
	{
		if ($attr->getValueCollectionTypeId() == IValueCollectionType::ID_NONE) {
			// Single value
			$this->insertValue($attr, $value);

		} else {
			// Array of values
			foreach ($value as $valueElement) {
				$this->insertValue($attr, $valueElement);
			}
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

		// Go through all attributes
		foreach ($this->attributeRepository->getAttributeSpecsByIds($attrIds) as $attr) {
			$this->saveValue($attr, $this->values[$attr->getId()]);
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

		// Go through all attributes to tell which are collections and which not
		$this->values = array();
		foreach ($this->attributeRepository->getAttributeSpecsByIds($attrIds) as $attr) {
			$attrId = $attr->getId();

			if ($attr->getValueCollectionTypeId() == IValueCollectionType::ID_NONE) {
				// Not a collection. Use first element of selected value array
				$this->values[$attrId] = $selectedValues[$attrId][0];
			} else {
				// Collection. Use whole selected value array
				$this->values[$attrId] = $selectedValues[$attrId];
			}
		}
	}
}
