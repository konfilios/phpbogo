<?php
/* 
 */

namespace Bogo\Facebook\Model;

class PlaceParking extends Object
{
	/**
	 * Indicates street parking is available.
	 *
	 * @var integer
	 */
	public $street;

	/**
	 * Indicates a parking lot is available.
	 *
	 * @var integer
	 */
	public $lot;

	/**
	 * Indicates a valet is available.
	 *
	 * @var integer
	 */
	public $valet;
}
