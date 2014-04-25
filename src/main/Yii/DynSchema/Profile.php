<?php
/*
 */

namespace Bogo\Yii\DynSchema;

use \CBehavior;
use \CComponent;

/**
 * Dynamic profile.
 * 
 * Aggregates a list of attributes as well as their corresponding data values.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Profile extends CComponent
{
	/**
	 * Attribute map.
	 * 
	 * Attribute id is the key.
	 * 
	 * @var Attribute[]
	 */
	private $_attributes = array();

	/**
	 * Value map.
	 * 
	 * Attribute id is the key.
	 *
	 * @var array 
	 */
	private $_values = array();

	/**
	 * Repository singleton.
	 *
	 * @return \Bogo\Yii\DynSchema\Repository
	 */
	public function getRepository()
	{
		return new Repository();
	}

	/**
	 * Attribute factory.
	 *
	 * @param integer $attrId
	 * @param string $attrLabel
	 * @param string $datatypeId
	 * @param array $datatypeParams
	 * @return Attribute
	 */
	public function createAttr($attrId, $attrLabel, $datatypeId, $datatypeParams)
	{
		$attr = new Attribute();
		$attr->id = $attrId;
		$attr->label = $attrLabel;
		$attr->datatype = $this->getRepository()->createDatatype($datatypeId, $datatypeParams);

		return $attr;
	}

	/**
	 * Add attribute to map.
	 * 
	 * @param Attribute $attr
	 */
	public function addAttr(Attribute $attr)
	{
		$this->_attributes[$attr->id] = $attr;
	}

	/**
	 * Get attribute from map by id.
	 *
	 * @param integer $attrId
	 * @return Attribute
	 */
	public function getAttr($attrId)
	{
		return $this->_attributes[$attrId];
	}

	/**
	 * Get whole attribute map.
	 *
	 * @return Attribute[]
	 */
	public function getAttrs()
	{
		return $this->_attributes;
	}

	/**
	 * Clear value map.
	 */
	public function clearValues()
	{
		$this->_values = array();
	}

	/**
	 * Set new value corresponding to given attrId.
	 *
	 * @param integer $attrId
	 * @param mixed $value
	 */
	public function setValue($attrId, $value)
	{
		$this->_values[$attrId] = $value;
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
		if (isset($this->_values[$attrId])) {
			return $this->_values[$attrId];
		} else {
			return $this->getAttr($attrId)->defaultValue;
		}
	}
}