<?php
namespace Bogo\Dataset\Grid;

/**
 * Row iterator of a dataset grid.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Row implements SeekableIterator, Countable
{
	/**
	 * Current row.
	 *
	 * @var integer
	 */
	private $i;

	/**
	 * Source data grid.
	 *
	 * @var IGrid
	 */
	private $grid;

	/**
	 * Construct row iterator for given $grid.
	 *
	 * @param IGrid $grid
	 * @param type $i
	 */
	public function __construct(IGrid $grid)
	{
		$this->grid = $grid;
	}

	/**
	 * Iterator current item.
	 *
	 * @return Iterator
	 */
	public function current()
	{
		return new Grid\Iterator\RowCell($this->grid, $this->i);
	}

	/**
	 * Iterator current row number.
	 *
	 * @return integer
	 */
	public function key()
	{
		return $this->i;
	}

	/**
	 * Iterator progress to next element.
	 */
	public function next()
	{
		$this->i++;
	}

	/**
	 * Iterator rewind to beginning.
	 */
	public function rewind()
	{
		$this->i = 0;
	}

	/**
	 * Iterator validation.
	 *
	 * @return boolean
	 */
	public function valid()
	{
		return ($this->i < $this->grid->getRowCount());
	}

	/**
	 * SeekableIterator seek.
	 *
	 * @param integer $position
	 */
	public function seek($position)
	{
		if ((0 > $position) || ($position >= $this->grid->getRowCount())) {
			throw new OutOfBoundsException("Invalid seek row position ($position)");
		}

		$this->i = $position;
	}

	/**
	 * Countable count.
	 *
	 * @return integer
	 */
	public function count()
	{
		return $this->grid->getRowCount();
	}
}
