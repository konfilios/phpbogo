<?php
/* 
 */

namespace Bogo\Facebook\Model;

class UserTagCollection extends Collection
{
	public function attributeTypes()
	{
		return parent::attributeTypes() + array(
			'data' => 'Bogo\Facebook\Model\Comment[]'
		);
	}

	/**
	 *
	 * @var Bogo\Facebook\Model\Comment[]
	 */
	public $data;
}
