<?php
/**
 * CBActiveIndexedRelationality.
 *
 * @since 1.3
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBActiveIndexedRelationality extends CBehavior
{
	/**
	 * Class name of related model.
	 *
	 * @var string
	 */
	public $modelClass;
	/**
	 * Column name of related model linking back to the owner model.
	 *
	 * @var string
	 */
	public $ownerFkName;
	/**
	 * Column name of related model used for indexing.
	 *
	 * @var string
	 */
	public $indexFkName;
	/**
	 * Relation name.
	 *
	 * @var string
	 */
	public $relationName;

	/**
	 * Attach behavior and add relations.
	 *
	 * @param CModel $owner
	 */
	public function attach($owner)
	{
		parent::attach($owner);

		$metadata = $this->owner->getMetadata();

		// Add indexed relation
		$metadata->addRelation($this->relationName, array(
			CActiveRecord::HAS_MANY,
			$this->modelClass,
			$this->ownerFkName,
			'index'=>$this->indexFkName
		));
	}

	/**
	 * Retrieve or initialize an empty l10n for passed languageId.
	 *
	 * @param mixed $index
	 * @return CActiveRecord[]
	 */
	public function &getOrCreateRelatedModel($index)
	{
		$relationName = $this->relationName;
		$relatedModels = $this->owner->$relationName;

		if (!isset($relatedModels[$index])) {
			$className = $this->modelClass;
			$ownerFkName = $this->ownerFkName;
			$indexFkName = $this->indexFkName;

			// Instantiate and initialize new l10n model
			$relatedModel = new $className();
			$relatedModel->$ownerFkName = $this->owner->id;
			$relatedModel->$indexFkName = $index;

			// Attach to owner's l10n array
			$relatedModels[$index] = $relatedModel;
			$this->owner->$relationName = $relatedModels;
		} else {
			$relatedModel = $this->owner->{$relationName}[$index];
		}

		return $relatedModel;
	}

	/**
	 * Save related models or throw exception.
	 */
	public function saveRelatedModelsOrThrow()
	{
		$relationName = $this->relationName;
		$ownerFkName = $this->ownerFkName;
		$indexFkName = $this->indexFkName;

		$failureCount = 0;
		$errorsAsStrings = '';
		foreach ($this->owner->$relationName as $index=>$relatedModel) {
			$relatedModel->$ownerFkName = $this->owner->id;
			$relatedModel->$indexFkName = $index;

			if (!$relatedModel->validate()) {
				$failureCount++;
				$errorsAsStrings .= $relatedModel->getErrorsAsString();
			}
		}

		if ($failureCount > 0) {
			throw new Exception('Failed to validate '.$failureCount.' '.$this->modelClass.' related models: '.$errorsAsStrings);
		}

		$savedIds = array();
		foreach ($this->owner->$relationName as $index=>$relatedModel) {
			$relatedModel->saveOrThrow();

			$savedIds[] = $relatedModel->id;
		}

		// Delete related models not wanted by user
		$modelClass = $this->modelClass;
		$criteria = new CDbCriteria();
		$criteria->compare($this->ownerFkName, $this->owner->id);
	
		if (!empty($savedIds)) {
			$criteria->addNotInCondition('id', $savedIds);
		}

		$modelController = new $modelClass;
		$modelController->deleteAll($criteria);
	}
	/**
	 * Batch assignment of related models' attributes.
	 */
	public function getRelatedModelsAttributes()
	{
		$relationName = $this->relationName;

		$indexedModelsAttributes = array();
		foreach ($this->owner->$relationName as $relatedModel) {
			$indexedModelsAttributes[$relatedModel->id] = $relatedModel->attributes;
		}

		return $indexedModelsAttributes;
	}

	/**
	 * Batch assignment of related models' attributes.
	 *
	 * @param type $indexedModelsAttributes
	 */
	public function setRelatedModelsAttributes($indexedModelsAttributes)
	{
		$relationName = $this->relationName;

		$relatedModels = array();
		foreach ($indexedModelsAttributes as $index=>$modelAttributes) {
			$relatedModel = $this->getOrCreateRelatedModel($index);

			$relatedModel->attributes = $modelAttributes;

			$relatedModels[$index] = $relatedModel;
		}

		$this->owner->$relationName = $relatedModels;
	}

	/**
	 * Retrieve only the indexes of related models.
	 *
	 * @return array
	 */
	public function getIndexes()
	{
		$relationName = $this->relationName;
		return array_keys($this->owner->$relationName);
	}

	/**
	 * Initialize (empty) related models using passed indexes.
	 *
	 * @param array $indexes
	 */
	public function setIndexes($indexes)
	{
		$relationName = $this->relationName;

		$relatedModels = array();
		foreach ($indexes as $index) {
			$relatedModel = $this->getOrCreateRelatedModel($index);

			$relatedModels[$index] = $relatedModel;
		}
		$this->owner->$relationName = $relatedModels;
	}
}
