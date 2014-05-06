<?php
/*
 */

namespace Bogo\DynSchema\Service;

/**
 * Attribute repository.
 *
 * Allows retrieval of attribute models.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IAttributeSpecRepository
{
	/**
	 * Get attribute models by their ids.
	 *
	 * @param array $ids
	 * @return array
	 */
	public function getAttributeSpecsByIds($ids);

	/**
	 * Get functional attribute component by its id.
	 *
	 * @param string|integer $id
	 * @return array
	 */
	public function getAttributeSpecById($id);
}
