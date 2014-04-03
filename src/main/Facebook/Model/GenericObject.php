<?php
/* 
 */

namespace Bogo\Facebook\Model;

class GenericObject extends NamedObject
{
	const TYPE_USER = "user";

	/**
	 * Type of object.
	 *
	 * @var string
	 */
	public $type;
}