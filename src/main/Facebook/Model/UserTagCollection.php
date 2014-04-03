<?php
/* 
 */

namespace Bogo\Facebook\Model;

class UserTagCollection extends Collection
{
	public function attributeTypes()
	{
		return parent::attributeTypes() + array(
			'data' => 'Bogo\Facebook\Model\UserTag[]'
		);
	}

	/**
	 *
	 * @var Bogo\Facebook\Model\UserTag[]
	 */
	public $data;
}
