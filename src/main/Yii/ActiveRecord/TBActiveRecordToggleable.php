<?php
/**
 * Implementation of IBActiveRecordTogglable.
 *
 * @since 1.2
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
trait TBActiveRecordToggleable
{
	/**
	 * Applies activity scope.
	 *
	 * @param boolean $isActive
	 * @return CBActiveRecord
	 */
	public function scopeIsActive($isActive = true)
	{
		$criteria = new CDbCriteria();
		$criteria->compare('isActive', $isActive ? 1 : 0);

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Queries for a $id=>$title hash of active records.
	 *
	 * @param string $orderBy Default ordering to apply.
	 * @return string[]
	 */
	public function queryActiveTitlesById($orderBy = 'title ASC')
	{
		return $this->scopeIsActive()->queryTitlesById($orderBy);
	}

	/**
	 * @param boolean $createIfNull
	 * @return CDbCriteria
	 */
	abstract public function getDbCriteria($createIfNull = true);

	/**
	 * Queries for a $id=>$title hash.
	 *
	 * @param string $orderBy Default ordering to apply.
	 * @return string[]
	 */
	abstract public function queryTitlesById($orderBy = 'title ASC');
}