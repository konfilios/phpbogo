<?php
namespace Bogo\Facebook\Query;
use Bogo\Facebook\FacebookManager;

/* 
 */
abstract class Base
{
	/**
	 *
	 * @var FacebookManager
	 */
	private $manager;

	private $userId = 'me';

	private $limit = 25;

	private $fields;
	private $with;

	/**
	 *
	 * @param FacebookManager $manager
	 */
	public function __construct($manager, $uri)
	{
		$this->manager = $manager;
	}

	public function setFields($fields)
	{
		$this->fields = $fields;
		return $this;
	}

	public function setWith($with)
	{
		$this->with = $with;
		return $this;
	}

	/**
	 * Id of user on behalf of whom the query will be executed.
	 *
	 * @param string $userId
	 * @return static
	 */
	public function ofUser($userId = 'me')
	{
		$this->userId = $userId;
		return $this;
	}

	/**
	 * Maximum number of objects to return.
	 *
	 * @param integer $limit
	 * @return static
	 */
	public function limit($limit)
	{
		$this->limit = $limit;
		return $this;
	}

	/**
	 * Execute an API query.
	 *
	 * @param string $resource
	 * @param array $params
	 * @return array
	 */
	protected function api($resource, $params)
	{
		$uri = $this->userId.'/'.$resource;

		if (!empty($params)) {
			$uri .= '?'.implode('&', $params);
		}

		error_log('Executing facebook query: '.$uri);
		return $this->manager->getConnector()->api($uri);
	}
}
