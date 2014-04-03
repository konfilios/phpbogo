<?php
namespace Bogo\Dataset;

/**
 * Indexed Dataset implementation for arrays with numeric indexes.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class IndexedArray implements IIndexedDataset
{
	/**
	 * Items to iterate over.
	 * @var mixed[]
	 */
	protected $items;

	/**
	 * Number of items.
	 *
	 * This is cached for performance.
	 *
	 * @var integer
	 */
	protected $itemCount;

	/**
	 * Construct datset.
	 *
	 * @param mixed[] $items
	 */
	public function __construct(array &$items)
	{
		$this->items = $items;
		$this->itemCount = count($this->items);
	}

	/**
	 * Retrieve item by index.
	 *
	 * @param integer $itemIndex
	 * @return mixed
	 */
	public function getValueByIndex($itemIndex)
	{
		return ((0 <= $itemIndex) && ($itemIndex < $this->itemCount)) ? $this->items[$itemIndex] : null;
	}

	/**
	 * Retrieve item key by index.
	 *
	 * @param integer $itemIndex
	 * @return mixed
	 */
	public function getKeyByIndex($itemIndex)
	{
		return $itemIndex;
	}

	/**
	 * Number of items.
	 *
	 * @return integer
	 */
	public function count()
	{
		return $this->itemCount;
	}
}
