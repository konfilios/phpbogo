<?php
/*
 */

namespace Bogo\DynSchema;

/**
 * DynSchema datatype.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IDatatype
{
	const COLLECTION_TYPE_NONE = 0;
	const COLLECTION_TYPE_SET = 1;
	const COLLECTION_TYPE_LIST = 2;

	/**
	 * Globally unique datatype classifier.
	 * 
	 * @return string
	 */
	public function getId();

	/**
	 * Collection type of datatype.
	 * 
	 * @see COLLECTION_TYPE constants.
	 * @return integer
	 */
	public function getCollectionType();
}
