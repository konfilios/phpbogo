<?php
/*
 */

namespace Bogo\Facebook\Model;

class CoverPhoto extends Object
{
	/**
	 * ID of the Photo that represents this cover photo.
	 * 
	 * @var integer
	 */
	public $id;

	/**
	 * URL to the Photo that represents this cover photo.
	 *
	 * @var string
	 */
	public $source;

	/**
	 * The vertical offset in pixels of the photo from the bottom.
	 *
	 * @var integer
	 */
	public $offset_y;

	/**
	 * The horizontal offset in pixels of the photo from the left.
	 *
	 * @var integer
	 */
	public $offset_x;
}
