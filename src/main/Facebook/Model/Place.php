<?php
/* 
 */

namespace Bogo\Facebook\Model;

class Place extends NamedObject
{
	public function attributeTypes()
	{
		return parent::attributeTypes() + array(
			'location' => 'Bogo\Facebook\Model\Location'
		);
	}

	/**
	 * Place location.
	 *
	 * @var Location
	 */
	public $location;
}
