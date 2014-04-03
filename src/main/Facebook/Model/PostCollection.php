<?php
/*
 */

namespace Bogo\Facebook\Model;

class PostCollection extends Collection
{
	public function attributeTypes()
	{
		return parent::attributeTypes() + array(
			'data' => 'Bogo\Facebook\Model\Post[]'
		);
	}

	/**
	 *
	 * @var Bogo\Facebook\Model\Post[]
	 */
	public $data;
}
