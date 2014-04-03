<?php
/* 
 */

namespace Bogo\Facebook\Model;

class UserCollection extends Collection
{
	public function attributeTypes()
	{
		return parent::attributeTypes() + array(
			'data' => 'Bogo\Facebook\Model\User[]'
		);
	}

	/**
	 *
	 * @var Bogo\Facebook\Model\UserTag[]
	 */
	public $data;
}
