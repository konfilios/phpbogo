<?php
namespace Bogo\Dataset\Grid;

/**
 * Cell iterator of a dataset grid row.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class RowCell implements Iterator, Countable
{
	/**
	 * Current row.
	 *
	 * @var integer
	 */
	private $i;

	/**
	 * Current column.
	 *
	 * @var integer
	 */
	private $j;

	/**
	 * Current item index.
	 *
	 * This must be updated every time $j changes!
	 *
	 * @var integer
	 */
	private $c;

	/**
	 * Source data grid.
	 *
	 * @var IGrid
	 */
	private $grid;

	/**
	 * Construct cell iterator for row $i of given $grid.
	 *
	 * @param IGrid $grid
	 * @param integer $i
	 */
	public function __construct($grid, $i)
	{
		$this->i = $i;
		$this->grid = $grid;
	}

	/**
	 * Iterator current item.
	 *
	 * @return mixed
	 */
	public function current()
	{
		return $this->grid->getDataset()->getValueByIndex($this->c);
	}

	/**
	 * Iterator current item key.
	 *
	 * @return integer
	 */
	public function key()
	{
		return $this->grid->getDataset()->getKeyByIndex($this->c);
	}

	/**
	 * Iterator progress to next element.
	 */
	public function next()
	{
		$this->j++;
		$this->c += $this->grid->getRowCount();
	}

	/**
	 * Iterator rewind to beginning.
	 */
	public function rewind()
	{
		$this->j = 0;
		$this->c = $this->i;
	}

	/**
	 * Iterator validation.
	 *
	 * @return boolean
	 */
	public function valid()
	{
		return ($this->j < $this->grid->getColumnCount());
	}

	/**
	 * SeekableIterator seek.
	 *
	 * @param integer $position
	 */
	public function seek($position)
	{
		if ((0 > $position) || ($position >= $this->grid->getColumnCount())) {
			throw new OutOfBoundsException("Invalid seek column ($position)");
		}
		$this->j = $position;
		$this->c = $this->j * $this->grid->getRowCount() + $this->i;
	}

	/**
	 * Countable count.
	 *
	 * @return integer
	 */
	public function count()
	{
		return $this->grid->getColumnCount();
	}
}
