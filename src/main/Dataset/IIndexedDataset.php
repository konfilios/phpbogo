<?php
namespace Bogo\Dataset;

/**
 * Allows value and key access by integer zero-based index.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IIndexedDataset extends Countable
{
	/**
	 * Retrieve item value by integer zero-based index.
	 *
	 * @param integer $itemIndex
	 * @return mixed
	 */
	public function getValueByIndex($itemIndex);

	/**
	 * Retrieve item key by integer zero-based index.
	 *
	 * @param integer $itemIndex
	 * @return mixed
	 */
	public function getKeyByIndex($itemIndex);
}
