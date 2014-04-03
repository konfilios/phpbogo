<?php
/* 
 */

namespace Bogo\Facebook\Model;

class MessageTag extends GenericObject
{
	/**
	 * Character offset of the object's string representation in the message.
	 *
	 * @var integer
	 */
	public $offset;

	/**
	 * Character length of the object's string representation in the message.
	 *
	 * @var integer
	 */
	public $length;
}
