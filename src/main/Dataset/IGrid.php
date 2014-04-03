<?php
namespace Bogo\Dataset;

/**
 * Interface for all dataset grids.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IGrid extends IteratorAggregate
{
	/**
	 * Number of columns in the grid.
	 *
	 * @return integer
	 */
	public function getColumnCount();

	/**
	 * Number of rows in the grid.
	 *
	 * @return integer
	 */
	public function getRowCount();

	/**
	 * Wrapped indexed dataset.
	 *
	 * @return IIndexedDataset
	 */
	public function &getDataset();
}
