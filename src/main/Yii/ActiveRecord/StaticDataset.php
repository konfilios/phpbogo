<?php
namespace Bogo\Yii\ActiveRecord;

/**
 * Static dataset.
 *
 * Inserts/updates record based on specific uniqueness criteria.
 *
 * This is a helper class proving itself when maintenance of reference tables becomes an issue.
 *
 * @since 1.0
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class StaticDataset extends \CComponent
{
	const INPUT_TYPE_RECORD_ARRAY = 1;
	const INPUT_TYPE_SINGLE_RECORD = 2;
	const INPUT_TYPE_ATTRIBUTE_PAIR = 3;
	const INPUT_TYPE_SINGLE_ATTRIBUTE = 4;

	/**
	 * \CActiveRecord subclass being manipulated.
	 *
	 * @var string
	 */
	protected $modelClass;

	/**
	 * Type of input dataset.
	 *
	 * @var integer
	 */
	protected $inputType = self::INPUT_TYPE_RECORD_ARRAY;

	/**
	 * Optional parameters refining the input type.
	 *
	 * Interpretion of these params depends on the input type selected.
	 *
	 * @var array
	 */
	protected $inputTypeParams = array();

	/**
	 * Foreign keys with parent object.
	 *
	 * Format: $thisModelAttribute=>$parentModelAttribute
	 *
	 * @var string[]
	 */
	protected $foreignKeys = array();

	/**
	 * Attributes to accompany verbose action messages.
	 *
	 * @var string[]
	 */
	protected $verboseAttributes = array();

	/**
	 * Names of search attributes which determine record uniqueness.
	 *
	 * @var string[]
	 */
	protected $uniqueSearchAttributes = array('id');

	/**
	 * Names of attributes which should be suppressed when inserting.
	 *
	 * @var string[]
	 */
	protected $suppressedInsertAttributes = array();

	/**
	 * Names of attributes which should be suppressed when updating.
	 *
	 * @var string[]
	 */
	protected $suppressedUpdateAttributes = array();

	/**
	 * If true, existing records will be updated when found.
	 * @var boolean
	 */
	protected $doUpdateIfFound = true;

	/**
	 * If true, records will be inserted when not found.
	 * @var boolean
	 */
	protected $doInsertIfNotFound = true;

	/**
	 * Hash of child refreshers.
	 *
	 * @var CBStaticDataset[]
	 */
	protected $children = array();

	/**
	 * Factory method.
	 *
	 * @param string|array $options Model class or an array of options including modelClass
	 * @return CBStaticDataset
	 */
	static public function create($options)
	{
		// Late static binding
		$selfClassName = get_called_class();

		if (is_string($options)) {
			// Model class has been given
			return new $selfClassName($options);
		} else {
			// Options have been given
			$refresher = new $selfClassName($options['modelClass']);

			// Unset to avoid setOptions exception
			unset($options['modelClass']);

			return $refresher->setOptions($options);
		}
	}

	/**
	 * Constructor.
	 *
	 * @param string $modelClass
	 */
	protected function __construct($modelClass)
	{
		$this->modelClass = $modelClass;
	}

	/**
	 * Get model's singleton.
	 *
	 * @return \CActiveRecord
	 */
	private function getModelSingleton()
	{
		return forward_static_call(array($this->modelClass, 'model'));
	}

	/**
	 * Get a new model instance.
	 *
	 * @return \CActiveRecord
	 */
	private function createModelInstance()
	{
		$className = $this->modelClass;
		return new $className();
	}

	/**
	 * Input type.
	 *
	 * @param integer|array $inputType
	 * @param array $inputTypeParams
	 */
	public function setInputType($inputType, $inputTypeParams = array())
	{
		if (is_array($inputType)) {
			// It should contain the input type and then input params
			$this->inputType = array_shift($inputType);
			$this->inputTypeParams = $inputType;
		} else {
			$this->inputType = $inputType;
			$this->inputTypeParams = $inputTypeParams;
		}
	}

	/**
	 * Names of search attributes which determine record uniqueness.
	 *
	 * @param string[] $foreignKeys
	 * @return CBStaticDataset
	 */
	public function setForeignKeys($foreignKeys)
	{
		$this->foreignKeys = $foreignKeys;
		return $this;
	}

	/**
	 * Names of search attributes which determine record uniqueness.
	 *
	 * Defaults to array('id').
	 *
	 * @param string[] $uniqueSearchAttributes
	 * @return CBStaticDataset
	 */
	public function setUniqueSearchAttributes($uniqueSearchAttributes)
	{
		$this->uniqueSearchAttributes = $uniqueSearchAttributes;
		return $this;
	}

	/**
	 * Names of attributes which should be suppressed when updating.
	 *
	 * @param string[] $suppressedUpdateAttributes
	 * @return CBStaticDataset
	 */
	public function setSuppressedUpdateAttributes($suppressedUpdateAttributes)
	{
		$this->suppressedUpdateAttributes = $suppressedUpdateAttributes;
		return $this;
	}

	/**
	 * Names of attributes which should be suppressed when inserting.
	 *
	 * @param string[] $suppressedInsertAttributes
	 * @return CBStaticDataset
	 */
	public function setSuppressedInsertAttributes($suppressedInsertAttributes)
	{
		$this->suppressedInsertAttributes = $suppressedInsertAttributes;
		return $this;
	}

	/**
	 * If true, records will be inserted when not found.
	 *
	 * Defaults to true.
	 *
	 * @param boolean $doInsertIfNotFound
	 * @return CBStaticDataset
	 */
	public function setDoInsertIfNotFound($doInsertIfNotFound)
	{
		$this->doInsertIfNotFound = $doInsertIfNotFound;
		return $this;
	}

	/**
	 * If true, existing records will be updated when found.
	 *
	 * Defaults to true.
	 *
	 * @param boolean $doUpdateIfFound
	 * @return CBStaticDataset
	 */
	public function setDoUpdateIfFound($doUpdateIfFound)
	{
		$this->doUpdateIfFound = $doUpdateIfFound;
		return $this;
	}

	/**
	 * If not empty, verbose action/record info is printed.
	 *
	 * Defaults to null.
	 *
	 * @param string $verboseAttributes
	 * @return CBStaticDataset
	 */
	public function setVerboseAttributes($verboseAttributes)
	{
		$this->verboseAttributes = $verboseAttributes;
		return $this;
	}

	/**
	 * Wrapper for \CActiveRecord::refreshMetaData().
	 *
	 * @return CBStaticDataset
	 */
	public function refreshMetaData()
	{
		$this->getModelSingleton()->refreshMetaData();
		return $this;
	}

	/**
	 * Child datasets.
	 *
	 * @param array $children
	 */
	public function setChildren($children)
	{
		foreach ($children as $name=>$refresher) {
			$this->addChild($name, $refresher);
		}
	}

	/**
	 * Add (and optionally create and initialize) a child refresher.
	 *
	 * @param string $name
	 * @param CBStaticDataset|array $refresher A refresher object or an array accepted by create() factory method.
	 * @return CBStaticDataset
	 */
	public function addChild($name, $refresher)
	{
		if (is_array($refresher)) {
			// Turn array into an object
			$refresher = CBStaticDataset::create($refresher);
		}

		$this->children[$name] = $refresher;

		return $this;
	}

	/**
	 * Remove a child refresher (if found).
	 *
	 * @param string $name
	 * @return CBStaticDataset
	 */
	public function removeChild($name)
	{
		if (isset($this->children[$name])) {
			unset($this->children[$name]);
		}
		return $this;
	}

	/**
	 * Refresh model metadata.
	 *
	 * @param boolean $doRefreshMetaData
	 */
	public function setDoRefreshMetaData($doRefreshMetaData)
	{
		if ($doRefreshMetaData) {
			$this->getModelSingleton()->refreshMetaData();
		}
	}

	/**
	 * Easy initializion using a single array of options.
	 *
	 * @param array $options
	 * @return CBStaticDataset
	 */
	public function setOptions($options)
	{
		// Copy safe options
		foreach ($options as $optionName=>$optionValue) {
			$setter = 'set'.ucfirst($optionName);
			$this->$setter($optionValue);
		}

		return $this;
	}

	/**
	 * Refresh a single model.
	 *
	 * @param array $inputRecord
	 * @param \CActiveRecord $parentModel
	 */
	private function refreshBySingleRecord($inputRecord, $parentModel = null)
	{
		// Copy foreign key attributes from parent
		if ($parentModel !== null) {
			foreach ($this->foreignKeys as $myAttributeName=>$parentAttributeName) {
				$inputRecord[$myAttributeName] = $parentModel->$parentAttributeName;
			}
		}

		// Determine search unique attributes
		$uniqueAttributes = array();
		foreach ($this->uniqueSearchAttributes as $attributeName) {
			if (!isset($inputRecord[$attributeName])) {
				throw new CException('Unique search attribute "'.$attributeName.'" of "'.$this->modelClass.'" not found in input record');
			}

			$uniqueAttributes[$attributeName] = $inputRecord[$attributeName];
		}

		// Determine verbose attributes
		$verboseAttributes = '';
		if (!empty($this->verboseAttributes)) {
			if (!isset($inputRecord[$attributeName])) {
				throw new CException('Verbose attribute "'.$attributeName.'" of "'.$this->modelClass.'" not found in input record');
			}

			foreach ($this->verboseAttributes as $attributeName) {
				$verboseAttributes .= ($verboseAttributes ? ', ' : '').$attributeName.': '.$inputRecord[$attributeName];
			}
		}

		// Search for existing model
		$persistedModel = $this->getModelSingleton()->findByAttributes($uniqueAttributes);

		if ($persistedModel === null) {
			if ($this->doInsertIfNotFound) {
				// Existing model not found, create a new one
				$persistedModel = $this->createModelInstance();

				foreach ($inputRecord as $attributeName=>$attributeValue) {
					// Go through all attributes
					if (!in_array($attributeName, $this->suppressedInsertAttributes) && !isset($this->children[$attributeName])) {
						// Pick only those not suppressed for insertion
						$persistedModel->$attributeName = $attributeValue;
					}
				}

				if (!empty($verboseAttributes)) {
					echo "Insert ".$this->modelClass." (".$verboseAttributes.")\n";
				}

				$persistedModel->insert();
			}
		} else {
			if ($this->doUpdateIfFound) {
				// Existing model found, update it though
				foreach ($inputRecord as $attributeName=>$attributeValue) {
					if (!in_array($attributeName, $this->suppressedUpdateAttributes) && !isset($this->children[$attributeName])) {
						// Go through all attributes
						$persistedModel->$attributeName = $attributeValue;
					}
				}

				if (!empty($verboseAttributes)) {
					echo "Update ".$this->modelClass." (".$verboseAttributes.")\n";
				}

				$persistedModel->update();
			}
		}

		if ($persistedModel !== null) {
			// Refresh our children. This implements the traversal of the hierarchy
			foreach ($this->children as $attributeName=>$refresher) {
				/* @var $refresher CBStaticDataset */
				if (isset($inputRecord[$attributeName])) {
					$refresher->refresh($inputRecord[$attributeName], $persistedModel);
				}
			}
		}
	}

	/**
	 * Refresh multiple models represented as array of assocs.
	 *
	 * @param array[] $inputRecordArray
	 * @param \CActiveRecord $parentModel
	 */
	private function refreshByRecordArray($inputRecordArray, $parentModel = null)
	{
		foreach ($inputRecordArray as $attributes) {
			$this->refreshBySingleRecord($attributes, $parentModel);
		}
	}

	/**
	 * Refresh multiple models which only have a pair of attributes each.
	 *
	 * @param array $inputAttributePairs
	 * @param string $keyAttributeName
	 * @param string $valueAttributeName
	 * @param \CActiveRecord $parentModel
	 */
	private function refreshByAttributePair($inputAttributePairs, $keyAttributeName, $valueAttributeName, $parentModel = null)
	{
		foreach ($inputAttributePairs as $key=>$value) {
			$this->refreshBySingleRecord(array(
				$keyAttributeName => $key,
				$valueAttributeName => $value,
			), $parentModel);
		}
	}

	/**
	 * Refresh one or multiple models using a single value.
	 *
	 * @param mixed $inputAttributeArray
	 * @param string $valueAttributeName
	 * @param \CActiveRecord $parentModel
	 */
	private function refreshBySingleAttribute($inputAttributeArray, $valueAttributeName, $parentModel = null)
	{
		if (is_array($inputAttributeArray)) {
			// Many records with single attribute name
			foreach ($inputAttributeArray as $value) {
				$this->refreshBySingleRecord(array(
					$valueAttributeName => $value,
				), $parentModel);
			}
		} else {
			// Single record with single attribute name
			$this->refreshBySingleRecord(array(
				$valueAttributeName => $inputAttributeArray,
			), $parentModel);
		}
	}

	/**
	 * Refresh $inputDataset.
	 *
	 * The inputDataset must be compatible with the inputMode of the refresher.
	 *
	 * @param mixed $inputDataset
	 * @param \CActiveRecord $parentModel
	 */
	public function refresh($inputDataset, $parentModel = null)
	{
		switch ($this->inputType) {
		case self::INPUT_TYPE_RECORD_ARRAY:
			return $this->refreshByRecordArray($inputDataset, $parentModel);

		case self::INPUT_TYPE_SINGLE_RECORD:
			return $this->refreshBySingleRecord($inputDataset, $parentModel);

		case self::INPUT_TYPE_ATTRIBUTE_PAIR:
			return $this->refreshByAttributePair($inputDataset, $this->inputTypeParams['keyAttr'], $this->inputTypeParams['valueAttr'], $parentModel);

		case self::INPUT_TYPE_SINGLE_ATTRIBUTE:
			return $this->refreshBySingleAttribute($inputDataset, $this->inputTypeParams['valueAttr'], $parentModel);
		}
	}
}