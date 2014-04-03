<?php
namespace Bogo\Dataset\Grid;
use Bogo\Dataset;

/**
 * Indexed Dataset grid implementation for fixed column count.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class FixedColumn implements Dataset\IGrid
{
	/**
	 * Items to iterate over.
	 * @var Dataset\IIndexedDataset
	 */
	protected $dataset;

	/**
	 * Grid column count.
	 *
	 * @var integer
	 */
	protected $columnCount;

	/**
	 * Grid row count.
	 * @var integer
	 */
	protected $rowCount;

	/**
	 * Construct grid.
	 *
	 * @param Dataset\IIndexedDataset $items
	 * @param integer $columnCount
	 */
	public function __construct(Dataset\IIndexedDataset $dataset, $columnCount)
	{
		$this->dataset = $dataset;

		$this->columnCount = $columnCount;
		$itemCount = count($dataset);
		$this->rowCount = ceil($itemCount / $this->columnCount);
	}

	/**
	 * Grid column count.
	 *
	 * @return integer
	 */
	public function getColumnCount()
	{
		return $this->columnCount;
	}

	/**
	 * Grid row count.
	 *
	 * @return integer
	 */
	public function getRowCount()
	{
		return $this->rowCount;
	}

	/**
	 * Wrapped indexed dataset.
	 *
	 * @return IIndexedDataset
	 */
	public function &getDataset()
	{
		return $this->dataset;
	}

	/**
	 * IteratorAggregate iterator.
	 *
	 * @return Grid\Iterator\Row
	 */
	public function getIterator()
	{
		return new Grid\Iterator\Row($this);
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
		return new Grid\FixedColumn(new IndexedArray($items), $columnCount);
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
		return new Grid\FixedColumn(new IndexedAssoc($items), $columnCount);
	}
}
