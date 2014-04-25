<?php
/*
 */

namespace Bogo\Yii\DynSchema;

use \Bogo\DynSchema\IDatatype;

/**
 * Base Yii datatype implementation.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Datatype extends \CComponent implements IDatatype
{
	private $_id;
	private $_collectionType;

	public function __construct($params)
	{
		foreach ($params as $field=>$value) {
			if ($this->hasProperty($field)) {
				$this->{$field} = $value;
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getId()
	{
		return $this->_id;
	}

	public function setId($id)
	{
		$this->_id = $id;
	}

	/**
	 * @inheritdoc
	 */
	public function getCollectionType()
	{
		return $this->_collectionType;
	}

	/**
	 * @inheritdoc
	 */
	public function setCollectionType($collectionType)
	{
		$this->_collectionType = $collectionType;
	}
}
