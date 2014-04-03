<?php
namespace Bogo\Facebook\Query;
use Bogo\Facebook\Model;
/* 
 */

class Feed extends Base
{
	/**
	 * Filter only posts with location.
	 *
	 * @var boolean
	 */
	private $withLocation = false;

	/**
	 * Filter only posts with location.
	 *
	 * @param boolean $requireLocation
	 * @return static
	 */
	public function withLocation($requireLocation = true)
	{
		$this->withLocation = $requireLocation;
		return $this;
	}

	/**
	 * Execute the query.
	 *
	 * @return Model\PostCollection
	 */
	public function findAll()
	{
		$params = array();

		if ($this->withLocation) {
			$params[] = 'with=location';
		}

		$result = $this->api('feed', $params);
//		print_r($result);
		return Model\PostCollection::createOne($result);
	}
}