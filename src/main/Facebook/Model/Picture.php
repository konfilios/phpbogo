<?php
/* 
 */

namespace Bogo\Facebook\Model;

class Picture extends Object
{
	public function attributeTypes()
	{
		return array(
			'data' => 'Bogo\Facebook\Model\PictureData'
		);
	}

	/**
	 * Picture data.
	 *
	 * @var PictureData
	 */
	public $data;
}