<?php
/**
 * Toggleable active records.
 *
 * Toggleable active records possess an isActive bit field which turns them on and off.
 *
 * @since 1.2
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IBActiveRecordToggleable
{
	/**
	 * Applies activity scope.
	 *
	 * @param boolean $isActive
	 * @return CActiveRecord
	 */
	public function scopeIsActive($isActive = true);

	/**
	 * Queries for a $id=>$title hash of active records.
	 *
	 * @param string $orderBy Default ordering to apply.
	 * @return string[]
	 */
	public function queryActiveTitlesById($orderBy = 'title ASC');
}