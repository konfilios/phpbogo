<?php
/*
 */

namespace Bogo\DynSchema\Service;

/**
 * Spec repository.
 *
 * Allows retrieval of specs which can then be used by an engine.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface ISpecRepository
{
	/**
	 * Get schema spec for given some attribute ids.
	 *
	 * @param array $ids
	 * @return array
	 */
	public function getSchemaSpecByAttributeIds($ids);
}
