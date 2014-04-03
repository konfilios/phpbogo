<?php
/* 
 */
namespace Bogo\Facebook\Model;

class CollectionPagingCursors extends Object
{
	/**
	 * This is the cursor that points to the end of the page of data that has been returned.
	 *
	 * @var string
	 */
	public $after;

	/**
	 * This is the cursor that points to the start of the page of data that has been returned.
	 *
	 * @var string
	 */
	public $before;
}
