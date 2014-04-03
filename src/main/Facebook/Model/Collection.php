<?php
/* 
 */

namespace Bogo\Facebook\Model;

class Collection extends Object
{
	public function attributeTypes()
	{
		return array(
			'paging' => 'Bogo\Facebook\Model\CollectionPaging'
		);
	}

	/**
	 * Endpoint data.
	 *
	 * @var NamedObject[]
	 */
	public $data;

	/**
	 * Pagination data.
	 *
	 * @var CollectionPaging
	 */
	public $paging;
}

