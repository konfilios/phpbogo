<?php
/*
 * https://developers.facebook.com/docs/reference/api/pagination/
 */

namespace Bogo\Facebook\Model;

class CollectionPaging extends Object
{
	public function attributeTypes()
	{
		return array(
			'cursors' => 'Bogo\Facebook\Model\CollectionPagingCursors'
		);
	}

	/**
	 *
	 * @var CollectionPagingCursors
	 */
	public $cursors;

	/**
	 * The Graph API endpoint that will return the previous page of data. If not included, this is the first page of data.
	 *
	 * @var string
	 */
	public $previous;

	/**
	 * The Graph API endpoint that will return the next page of data. If not included, this is the last page of data.
	 *
	 * @var string
	 */
	public $next;
}