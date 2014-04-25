<?php
/**
 * Extend base CActiveRecord functionality with handy functions.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBActiveRecord extends CActiveRecord
{
	/**
	 * Transform model array into a custom array/hash.
	 *
	 * Variation of CHtml::listData with different argument order,
	 * allowing array results when $keyAttr equals null.
	 *
	 * @param array $models Array of source models.
	 * @param mixed $valueAttr Attribute to be used as value. If null, the model itself is used as is.
	 * @param mixed $keyAttr Attribute to be used as key. If null, an array is returned instead of an assoc.
	 * @return array
	 */
	static public function listData(array $models, $valueAttr = null, $keyAttr = null)
	{
		$values = array();

		// Go through all models
		foreach ($models as $model) {
			// Retrieve value
			$value = ($valueAttr === null) ? $model : self::value($model, $valueAttr);

			if ($keyAttr === null) {
				// Array mode (no key)
				$values[] = $value;
			} else {
				// Assoc mode
				$values[self::value($model, $keyAttr)] = $value;
			}
		}

		return $values;
	}

	/**
	 * Variation of CHtml::value retrieving model values.
	 *
	 * This variation does not accept default values and optimizes the isset conditions.
	 *
	 * @param mixed $model Source model
	 * @param mixed $valueAttr
	 * @return mixed
	 */
	public static function value($model, $valueAttr)
	{
		if (is_scalar($valueAttr)) {
			foreach (explode('.', $valueAttr) as $valuAttrComponent) {
				if (is_object($model)) {
					if (!isset($model->$valuAttrComponent)) {
						return null;
					}
					$model=$model->$valuAttrComponent;

				} else if(is_array($model)) {
					if (!isset($model[$valuAttrComponent])) {
						return null;
					}
					$model=$model[$valuAttrComponent];
				} else {
					return null;
				}
			}
		} else {
			return call_user_func($valueAttr,$model);
		}

		return $model;
	}

	/**
	 * Utc datetime to datetime stamp.
	 *
	 * @param integer $stamp
	 * @return string
	 */
	public function stampToUdatetime($stamp = null)
	{
		if (!$stamp) {
			$stamp = time();
		}
		return gmdate('Y-m-d H:i:s', $stamp);
	}

	/**
	 * getErrors() as string with newlines.
	 *
	 * @param string $attribute
	 * @return string
	 */
	public function getErrorsAsString($attribute = null)
	{
		$errorString = '';
		foreach (parent::getErrors($attribute) as $errorArray) {
			$errorString .= implode("\n", $errorArray)."\n";
		}
		return $errorString;
	}

	/**
	 * Select specific data.
	 * @param string $select
	 * @return CBActiveRecord
	 */
	public function scopeSelect($select)
	{
		$this->getDbCriteria()->select = $select;

		return $this;
	}

	/**
	 * Limit results.
	 *
	 * Singleton method.
	 *
	 * @param integer $limit Number of results.
	 * @return CBActiveRecord
	 */
	public function scopeLimit($limit)
	{
		$this->getDbCriteria()->limit = intval($limit);

		return $this;
	}

	/**
	 * Zero-based offset of results.
	 *
	 * Singleton method.
	 *
	 * @param integer $offset Offset of results.
	 * @return CBActiveRecord
	 */
	public function scopeOffset($offset)
	{
		$this->getDbCriteria()->offset = intval($offset);

		return $this;
	}

	/**
	 * Limit results.
	 *
	 * Singleton method.
	 *
	 * @param integer $order Order by clause.
	 * @return CBActiveRecord
	 */
	public function scopeOrderBy($order)
	{
		$this->getDbCriteria()->order = $order;

		return $this;
	}

	/**
	 * Applies criteria based on specified attribute values.
	 *
	 * See {@link find()} for detailed explanation about $condition and $params.
	 *
	 * @param array $attributes list of attribute values (indexed by attribute names) that the active records should match.
	 * An attribute value can be an array which will be used to generate an IN condition.
	 * @param mixed $condition query condition or criteria.
	 * @param array $params parameters to be bound to an SQL statement.
	 * @return CBActiveRecord
	 */
	public function scopeByAttributes($attributes,$condition='',$params=array())
	{
		$prefix=$this->getTableAlias(true).'.';
		$criteria=$this->getCommandBuilder()->createColumnCriteria($this->getTableSchema(),$attributes,$condition,$params,$prefix);
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}

	/**
	 * Distinct select.
	 *
	 * @return CBActiveRecord
	 */
	public function scopeDistinct()
	{
		$this->getDbCriteria()->distinct = true;

		return $this;
	}

	/**
	 * Execute current criteria in batches of given size.
	 *
	 * This is especially useful if you have <b>lots</b> of data to process and you do not want
	 * to (and you should not) download them all in your memory using a single query.
	 *
	 * Here's an example of a query retrieving 250.000 records at once:
	 * <pre>
	 * foreach (MyModel::model()->scopesAndCriteria()->findAll() as $myModel) {
	 * }
	 * </pre>
	 *
	 * Here's how it's transformed to access the records in 250 queries/batches of 1.000 records each:
	 * foreach (MyModel::model()->scopesAndCriteria()->findAllInBatchesOf(1000) as $myModel) {
	 * }
	 *
	 * The batched queries are performed transparently.
	 *
	 * Make sure you supply an order column, otherwise RDBMS systems like SQL Server will likely
	 * return non-sense.
	 *
	 * @param integer $batchSize
	 * @param string $orderBy
	 * @return CBActiveRecordBatchIterator
	 */
	public function findAllInBatchesOf($batchSize, $orderBy = null)
	{
		if ($orderBy !== null) {
			$this->scopeOrderBy($orderBy);
		}

		return new CBActiveRecordBatchIterator($this, $batchSize);
	}

	/**
	 * Queries column data from multiple rows into an array (assoc or simple).
	 *
	 * Creates an assoc/simple array where keys are extraced from $keyColumn of each selected record
	 * and values are taken from $valueColumn. There are 4 possible formats for your results,
	 * based on the input you give:
	 *
	 * <ul>
	 * <li>$valueColumns is a string, no $keyColumn:<br/ >
	 * $result = array(
	 *		$model_1->$valueColumn,
	 *		$model_2->$valueColumn,
	 *		:
	 * );
	 * </li>
	 * <li>$valueColumns is a string, $keyColumn is set:<br/ >
	 * $result = array(
	 *		$model_1->$keyColumn => $model_1->$valueColumn,
	 *		$model_2->$keyColumn => $model_2->$valueColumn,
	 *		:
	 * );
	 * </li>
	 * <li>$valueColumns is a string array, no $keyColumn:<br/ >
	 * $result = array(
	 *		array(
	 *			$valueColumn_1 => $model_1->$valueColumn_1,
	 *			$valueColumn_2 => $model_1->$valueColumn_2,
	 *			:
	 *		),
	 *		array(
	 *			$valueColumn_1 => $model_2->$valueColumn_1,
	 *			$valueColumn_2 => $model_2->$valueColumn_2,
	 *			:
	 *		),
	 *		:
	 * );
	 * </li>
	 * <li>$valueColumns is a string, $keyColumn is set:<br/ >
	 * $result = array(
	 *		$model_1->$keyColumn => array(
	 *			$valueColumn_1 => $model_1->$valueColumn_1,
	 *			$valueColumn_2 => $model_1->$valueColumn_2,
	 *			:
	 *		),
	 *		$model_2->$keyColumn => array(
	 *			$valueColumn_1 => $model_2->$valueColumn_1,
	 *			$valueColumn_2 => $model_2->$valueColumn_2,
	 *			:
	 *		),
	 *		:
	 * );
	 * </li>
	 * </ul>
	 *
	 * @param array|string $valueColumns A single value column (string) or multiple value columns (array of strings)
	 * @param string $keyColumn A single key column (string) for an assoc result, or null for a simple array result.
	 * @return array|array[]
	 */
	public function queryAllColumnData($valueColumns, $keyColumn = null)
	{
		// We'll select the key column for sure
		$selectColumns = $keyColumn ?: '';

		// Init hash
		$finalArray = array();

		if (is_array($valueColumns)) {
			//
			// Array value structure
			//

			// Compile list of columns to be selected
			foreach ($valueColumns as $valueColumn) {
				$selectColumns .= ($selectColumns ? ',' : '').$valueColumn;
			}

			// Apply select column list
			$this->scopeSelect($selectColumns);

			// Find records
			foreach ($this->findAll() as $row) {
				// Compile array value
				$values = array();
				foreach ($valueColumns as $valueColumn) {
					$values[$valueColumn] = $row->$valueColumn;
				}

				// Insert to final array
				if ($keyColumn) {
					// As a hash
					$finalArray[$row->$keyColumn] = $values;
				} else {
					// As a simple array
					$finalArray[] = $values;
				}
			}
		} else {
			// Scalar value structure
			$valueColumn = $valueColumns;

			// Compile list of columns to be selected
			$selectColumns .= ($selectColumns ? ',' : '').$valueColumn;

			// Apply select column list
			$this->scopeSelect($selectColumns);

			// Find records
			foreach ($this->findAll() as $row) {
				// Insert to final array
				if ($keyColumn) {
					// As a hash
					$finalArray[$row->$keyColumn] = $row->$valueColumn;
				} else {
					// As a simple array
					$finalArray[] = $row->$valueColumn;
				}
			}
		}

		return $finalArray;
	}

	/**
	 * Queries column data from a single row.
	 *
	 * There are 2 possible formats for your results, based on the input you give:
	 *
	 * <ul>
	 * <li>$valueColumns is a string: If no row is found, null is returned, otherwise:<br/ >
	 * $result = $foundModel->$valueColumn
	 * </li>
	 * <li>$valueColumns is a string array: If no row is found, empty array is returned, otherwise:<br/ >
	 * $result = array(
	 *		$valueColumn_1 => $foundModel->$valueColumn_1,
	 *		$valueColumn_2 => $foundModel->$valueColumn_2,
	 *		:
	 * );
	 * </li>
	 * </ul>
	 *
	 * @param array|string $valueColumns
	 * @return string|array
	 */
	public function queryColumnData($valueColumns)
	{
		$finalArray = $this->queryAllColumnData($valueColumns);

		if (empty($finalArray)) {
			return is_array($valueColumns) ? array() : null;
		} else {
			return $finalArray[0];
		}
	}

	/**
	 * Queries for a $id=>$title hash.
	 *
	 * @param string $orderBy Default ordering to apply.
	 * @return string[]
	 */
	public function queryTitlesById($orderBy = 'title ASC')
	{
		return $this->scopeOrderBy($orderBy)->queryAllColumnData('title', 'id');
	}

	/**
	 * Save or throw exception on error.
	 *
	 * This differs from the standard save() in that save() returns false on error instead
	 * of throwing an exception.
	 *
	 * @param boolean $runValidation Run validation prior to saving.
	 * @param array $attributes Attributes to save.
	 * @throws Exception
	 */
	public function saveOrThrow($runValidation=true,$attributes=null)
	{
		if (!$this->save($runValidation, $attributes)) {
			throw new Exception($this->getErrorsAsString());
		}
	}

	/**
	 * Wrap $taskCallback into a transaction and run it.
	 *
	 * @param callable $taskCallback
	 * @throws Exception
	 */
	public function runTransaction($taskCallback)
	{
		// Begin transaction
		$trn = $this->getDbConnection()->beginTransaction();

		try {
			// Execute task
			$taskCallback();

			// Commit changes
			$trn->commit();
		} catch (Exception $ex) {
			// Rollback
			$trn->rollback();

			// Rethrow
			throw $ex;
		}
	}

	/**
	 * FindByPk() or throw a not found exception.
	 *
	 * @param mixed $pk
	 * @return static
	 * @throws CException
	 */
	public function findByPkOrThrow($pk)
	{
		$model = $this->findByPk($pk);

		if (!$model) {
			throw new CException(Yii::t('CBActiveRecord',
				get_class($this).' with id "{pk}" was not found', array(
					'{pk}' => $pk,
				)));
		}
		
		return $model;
	}
}
