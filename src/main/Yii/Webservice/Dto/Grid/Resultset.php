<?php
/*
 */

namespace Bogo\Yii\Webservice\Dto\Grid;
use Bogo\Yii\Webservice;

/**
 * Base grid resultset.
 *
 * @since 1.2
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Resultset extends Webservice\Dto
{
	/**
	 * Offset of results.
	 *
	 * @var array
	 */
	public $items;

	/**
	 * Total number of items matching the query.
	 *
	 * Used for client-side pagination.
	 *
	 * @var integer
	 */
	public $totalCount;

	/**
	 * Query sequence number.
	 *
	 * @var integer
	 */
	public $sequence;

	/**
	 * Get type of a single item based on the type of `items`.
	 *
	 * @return string
	 * @throws Exception
	 * @ignore
	 */
	private function getItemType()
	{
		$attributeTypes = $this->attributeTypes();

		if (empty($attributeTypes['items'])) {
			return null;
		}

		$itemsType = $attributeTypes['items'];

		if ($itemsType == 'array') {
			return null;
		}

		if (substr($itemsType, -2) != '[]') {
			throw new \CHttpException(500, get_class($this).'.items property should be of array type', 500);
		}

		return substr($itemsType, 0, -2);
	}

	/**
	 * Create a grid results object.
	 *
	 * @param \CActiveRecord $itemFinder
	 * @param \CActiveRecord $itemCounter
	 * @param Query $query
	 * @return static
	 * @ignore
	 */
	static public function createPaginated(\CActiveRecord $itemFinder, \CActiveRecord $itemCounter, Query $query)
	{
		$query->validate();

		$results = new static();

		//
		// Apply all search parameters first
		//

		// Apply filters both to the finder and counter
		$query->applyFinderFilters($itemFinder);
		$query->applyCounterFilters($itemCounter);

		// Apply pagination to item finder
		$query->applyPaging($itemFinder);

		// Apply ordering to finder
		$query->applyOrderBy($itemFinder);

		//
		// Echo back sequence number
		//
		$results->sequence = $query->sequence;

		//
		// Get total count
		//
		$results->totalCount = intval($itemCounter->count());

		//
		// Get items
		//
		$foundItems = $itemFinder->findAll();
		$itemType = $results->getItemType();

		if (empty($itemType)) {
			$results->items = $foundItems;
		} else {
			$results->items = $itemType::createMany($foundItems);
		}

		return $results;
	}
}
