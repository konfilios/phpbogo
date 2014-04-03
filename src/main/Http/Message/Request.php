<?php
/*
 */

namespace Bogo\Http\Message;

/**
 * Request HTTP Message.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Request extends Base
{
	/**
	 * HTTP Request Verb/Method.
	 *
	 * Values are GET, POST, PUT, DELETE, etc.
	 *
	 * @var string
	 */
	private $httpVerb;

	/**
	 * HTTP Request URI.
	 *
	 * @var type
	 */
	private $uri;

	/**
	 * Attached files.
	 *
	 * @var string[]
	 */
	private $files = array();

	/**
	 * HTTP GET parameters.
	 *
	 * @var string[]
	 */
	private $queryParams = array();

	/**
	 * HTTP POST parameters.
	 *
	 * @var string[]
	 */
	private $postParams = array();

	/**
	 * User-defined fields.
	 *
	 * @var array
	 */
	private $userFields = array();

	/**
	 * HTTP Request Verb/Method.
	 *
	 * @return string
	 */
	public function getHttpVerb()
	{
		return $this->httpVerb;
	}

	/**
	 * HTTP Request Verb/Method.
	 *
	 * @param type $httpVerb
	 * @return Message\Request
	 */
	public function setHttpVerb($httpVerb)
	{
		$this->httpVerb = $httpVerb;

		return $this;
	}

	/**
	 * HTTP Request URI.
	 *
	 * @return string
	 */
	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * HTTP Request URI.
	 *
	 * @param string $uri
	 * @return Message\Request
	 */
	public function setUri($uri)
	{
		$this->uri = $uri;

		return $this;
	}

	/**
	 * Batch assignment of user fields.
	 *
	 * @param array $fields
	 * @return Message\Request
	 */
	public function setUserFields(array $fields)
	{
		foreach ($fields as $field=>$value) {
			$this->setUserField($field, $value);
		}

		return $this;
	}

	/**
	 * Set a user field.
	 *
	 * If value is null, then the user field is removed (if it's there).
	 *
	 * @param string $field Name of request header.
	 * @param string $value Value of request header.
	 * @return Message\Request
	 */
	public function setUserField($field, $value = null)
	{
		if ($value === null) {
			if (isset($this->userFields[$field])) {
				unset($this->userFields[$field]);
			}
		} else {
			$this->userFields[$field] = $value;
		}

		return $this;
	}

	/**
	 * Retrieve a specific USER field or all.
	 *
	 * @param string $field Parameter name.
	 * @return mixed
	 */
	public function getUserField($field = null)
	{
		return ($field === null) ? $this->userFields : (isset($this->userFields[$field]) ? $this->userFields[$field] : null);
	}

	/**
	 * Batch assignment of files.
	 *
	 * @param array $fields
	 * @return Message\Request
	 */
	public function setFiles(array $fields)
	{
		foreach ($fields as $field=>$value) {
			$this->setFile($field, $value);
		}

		return $this;
	}

	/**
	 * Queue $filename for uploading as $field.
	 *
	 * @param string $field Name of file in the request.
	 * @param string $value Filename to upload or null to unset.
	 * @return Message\Request
	 */
	public function setFile($field, $value = null)
	{
		if (is_null($value)) {
			// Unsetting
			if (isset($this->files[$field])) {
				unset($this->files[$field]);
			}
		} else {
			// Setting new value
			$this->files[$field] = $value;
		}

		return $this;
	}

	/**
	 * Retrieve a specific FILE field or all.
	 *
	 * @param string $field Parameter name.
	 * @return mixed
	 */
	public function getFile($field = null)
	{
		return ($field === null) ? $this->files : (isset($this->files[$field]) ? $this->files[$field] : null);
	}

	/**
	 * Batch assignment of GET parameters.
	 *
	 * @param array $fields
	 * @return Message\Request
	 */
	public function setQueryParams(array $fields)
	{
		foreach ($fields as $field=>$value) {
			$this->setQueryParam($field, $value);
		}

		return $this;
	}

	/**
	 * Set value for a GET field.
	 *
	 * @param string $field Parameter name.
	 * @param string $value New value or null to unset.
	 * @return Message\Request
	 */
	public function setQueryParam($field, $value = null)
	{
		if (is_null($value)) {
			// Unsetting
			if (isset($this->queryParams[$field])) {
				unset($this->queryParams[$field]);
			}
		} else {
			// Setting new value
			$this->queryParams[$field] = $value;
		}

		return $this;
	}

	/**
	 * Retrieve a specific GET parameter or all.
	 *
	 * @param string $field Parameter name.
	 * @return mixed
	 */
	public function getQueryParam($field = null)
	{
		return ($field === null) ? $this->queryParams : (isset($this->queryParams[$field]) ? $this->queryParams[$field] : null);
	}

	/**
	 * Batch assignment of POST parameters.
	 *
	 * @param array $fields
	 * @return Message\Request
	 */
	public function setPostParams(array $fields)
	{
		foreach ($fields as $field=>$value) {
			$this->setPostParam($field, $value);
		}

		return $this;
	}

	/**
	 * Set value for a POST field.
	 *
	 * @param string $field Parameter name.
	 * @param string $value New value or null to unset.
	 * @return Message\Request
	 */
	public function setPostParam($field, $value = null)
	{
		if (is_null($value)) {
			// Unsetting
			if (isset($this->postParams[$field])) {
				unset($this->postParams[$field]);
			}
		} else {
			$this->postParams[$field] = $value;
		}

		return $this;
	}

	/**
	 * Retrieve a specific POST parameter or all.
	 *
	 * @param string $field Parameter name.
	 * @return mixed
	 */
	public function getPostParam($field = null)
	{
		return ($field === null) ? $this->postParams : (isset($this->postParams[$field]) ? $this->postParams[$field] : null);
	}

	/**
	 * Wrap into a call.
	 *
	 * @return Call
	 */
	public function createCall($callClass = 'CallCurl')
	{
		return new $callClass($this);
	}

	/**
	 * Create a new HTTP Request message.
	 *
	 * @param string $httpVerb
	 * @param string $uri
	 * @return Message\Request
	 */
	static public function create($httpVerb = null, $uri = null)
	{
		$message = new Message\Request();

		$message->httpVerb = $httpVerb;
		$message->uri = $uri;

		return $message;
	}
}