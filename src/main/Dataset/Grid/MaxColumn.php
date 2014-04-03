<?php
namespace Bogo\Dataset\Grid;

/**
 * Indexed Dataset grid implementation for max column count.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class MaxColumn extends Grid\FixedColumn
{
	/**
	 * Construct grid.
	 *
	 * @param IIndexedDataset $items
	 * @param integer $columnCount
	 */
	public function __construct(IIndexedDataset $dataset, $columnCount)
	{
		$this->dataset = $dataset;

		$this->columnCount = $columnCount;
		$itemCount = count($dataset);
		$this->rowCount = ceil($itemCount / $this->columnCount);

		if (($this->rowCount > 0) && ($itemCount <= ($this->columnCount - 1) * $this->rowCount)) {
			$this->columnCount = ceil($itemCount / $this->rowCount);
		}
	}

	/**
	 * Construct a max column grid and wrap the dataset in an indexed array.
	 *
	 * @param mixed[] $items
	 * @param integer $columnCount
	 * @return Grid\MaxColumn
	 */
	static public function fromArray(array &$items, $columnCount)
	{
		return new Grid\MaxColumn(new IndexedArray($items), $columnCount);
	}

	/**
	 * Construct a max column grid and wrap the dataset in an indexed array.
	 *
	 * @param mixed[] $items
	 * @param integer $columnCount
	 * @return Grid\MaxColumn
	 */
	static public function fromAssoc(array &$items, $columnCount)
	{
		return new Grid\MaxColumn(new IndexedAssoc($items), $columnCount);
	}
}
