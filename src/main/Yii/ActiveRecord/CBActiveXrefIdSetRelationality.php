<?php
/**
 * CBActiveSetRelationality.
 *
 * @since 1.3
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBActiveXrefIdSetRelationality extends CBActiveIndexedRelationality
{
	/**
	 * Class name of cross-reference model.
	 *
	 * @var string
	 */
	public $xrefClass;
	/**
	 * Relation name.
	 *
	 * @var string
	 */
	public $xrefRelationName;

	/**
	 * Attach behavior and add relations.
	 *
	 * @param CModel $owner
	 */
	public function attach($owner)
	{
		parent::attach($owner);

		$metadata = $this->owner->getMetadata();

		// Add many-many relation
		$metadata->addRelation($this->xrefRelationName, array(
			CActiveRecord::MANY_MANY,
			$this->xrefClass,
			$this->modelClass.'('.$this->ownerFkName.','.$this->indexFkName.')',
		));
	}

	/**
	 * Filter models which are related to given indexes.
	 *
	 * @param type $relatedIndexes
	 * @param type $selectFields Fields of xref table to select.
	 * @return CActiveRecord
	 */
	public function scopeFilterRelatedIndexes($relatedIndexes, $selectFields = false)
	{
		$onCondition = $this->relationName.'.'.$this->indexFkName.' IN ('.implode(', ', $relatedIndexes).')';

		return $this->owner->with(array($this->relationName=>array(
			'select' => $selectFields,
			'on' => $onCondition,
			'joinType' => 'INNER JOIN',
			'together' => true,
		)));
	}
}
