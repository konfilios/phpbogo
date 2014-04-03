<?php
/**
 * CBActiveSessionableIndexedRelationality.
 *
 * @since 1.3
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBActiveSessionableIndexedRelationality extends CBActiveIndexedRelationality
{
	/**
	 * Name of session relation.
	 *
	 * @var string
	 */
	public $sessionRelationName;

	/**
	 * Index value used for session relation.
	 *
	 * @var mixed
	 */
	public $sessionIndex;

	/**
	 * Attach behavior and add relations.
	 *
	 * @param CModel $owner
	 */
	public function attach($owner)
	{
		parent::attach($owner);

		$metadata = $this->owner->getMetadata();
		$sessionIndex = is_callable($this->sessionIndex) ? call_user_func($this->sessionIndex) : $this->sessionIndex;

		// Add sessionL10n relation
		$metadata->addRelation($this->sessionRelationName, array(
			CActiveRecord::HAS_ONE,
			$this->modelClass,
			$this->ownerFkName,
			'on' => $this->sessionRelationName.".".$this->indexFkName." = '".$sessionIndex."'"
		));
	}
}
