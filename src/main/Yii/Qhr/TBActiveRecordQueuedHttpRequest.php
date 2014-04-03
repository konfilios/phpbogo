<?php
/**
 * A typical ActiveRecord implementation of IBQueuedHttpRequest.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
trait TBActiveRecordQueuedHttpRequest
{
	/**
	 * Update last attempt fields.
	 */
	public function logLastAttempt()
	{
		$this->attemptCount++;
		$this->lastAttemptUdatetime = gmdate('Y-m-d H:i:s');
	}

	/**
	 * Update last error fields.
	 *
	 * @param integer $errorCode
	 * @param string $errorText
	 */
	public function logLastError($errorCode, $errorText)
	{
		$this->lastErrorCode = $errorCode;
		$this->lastErrorText = $errorText;
	}

	/**
	 * Id.
	 *
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Request has succeeded.
	 *
	 * @return boolean
	 */
	public function getIsSuccessful()
	{
		return ($this->stateId == IBQueuedHttpRequest::STATE_SUCCESS);
	}

	/**
	 * Request has failed.
	 *
	 * @return boolean
	 */
	public function getIsFailed()
	{
		return ($this->stateId == IBQueuedHttpRequest::STATE_FAILED);
	}

	/**
	 * Request is pending.
	 *
	 * @return boolean
	 */
	public function getIsPending()
	{
		return ($this->stateId == IBQueuedHttpRequest::STATE_PENDING);
	}

	/**
	 * Last error message.
	 *
	 * @return string
	 */
	public function getLastErrorText()
	{
		return $this->lastErrorText;
	}

	/**
	 * Last error code.
	 *
	 * @return integer
	 */
	public function getLastErrorCode()
	{
		return $this->lastErrorCode;
	}

	/**
	 * Next scheduled attempt timestamp.
	 *
	 * @return integer
	 */
	public function getNextAttemptStamp()
	{
		return strtotime($this->nextAttemptUdatetime);
	}

	/**
	 * Request family id.
	 *
	 * @return integer
	 */
	public function getFamilyId()
	{
		return $this->familyId;
	}

	/**
	 * Create http request message from queued http message.
	 *
	 * @return CBHttpMessageRequest
	 */
	public function toHttpRequestMessage()
	{
		$requestMessage = CBHttpMessageRequest::create($this->httpVerb, $this->uri);

		$requestParams = json_decode($this->paramsJson, true);

		if (!empty($requestParams['headers'])) {
			$requestMessage->setHeaders($requestParams['headers']);
		}

		if (!empty($requestParams['getParams'])) {
			$requestMessage->setGetParams($requestParams['getParams']);
		}

		if (!empty($requestParams['postParams'])) {
			$requestMessage->setGetParams($requestParams['postParams']);
		}

		if (!empty($requestParams['userFields'])) {
			$requestMessage->setUserFields($requestParams['userFields']);
		}

		return $requestMessage;
	}

	/**
	 * Copy related attributes from http request message.
	 *
	 * @param CBHttpMessageRequest $message
	 */
	public function fromHttpRequestMessage(CBHttpMessageRequest $message)
	{
		$this->httpVerb = $message->getHttpVerb();

		$this->uri = $message->getUri();

		$params = array(
			'headers'=>$message->getHeader(),
			'getParams'=>$message->getGetParam(),
			'postParams'=>$message->getPostParam(),
			'userFields'=>$message->getUserField()
		);

		// Don't serialize empty arrays
		foreach ($params as $name=>&$value) {
			if (empty($value)) {
				unset($params[$name]);
			}
		}

		$this->paramsJson = json_encode($params);
	}
}
