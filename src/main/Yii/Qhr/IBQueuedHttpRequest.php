<?php
/**
 * Persistent queued http request items.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IBQueuedHttpRequest
{
	const STATE_PENDING = 1;
	const STATE_SUCCESS = 2;
	const STATE_FAILED = 3;

	/**
	 * Id.
	 *
	 * @return mixed
	 */
	public function getId();

	/**
	 * Request has succeeded.
	 *
	 * @return boolean
	 */
	public function getIsSuccessful();

	/**
	 * Request has failed.
	 *
	 * @return boolean
	 */
	public function getIsFailed();

	/**
	 * Request is pending.
	 *
	 * @return boolean
	 */
	public function getIsPending();

	/**
	 * Last error message.
	 *
	 * @return string
	 */
	public function getLastErrorText();

	/**
	 * Last error code.
	 *
	 * @return integer
	 */
	public function getLastErrorCode();

	/**
	 * Next scheduled attempt timestamp.
	 *
	 * @return integer
	 */
	public function getNextAttemptStamp();

	/**
	 * Request family id.
	 *
	 * @return integer
	 */
	public function getFamilyId();

	/**
	 * Create http request message from queued http message.
	 *
	 * @return CBHttpMessageRequest
	 */
	public function toHttpRequestMessage();

	/**
	 * Copy related attributes from http request message.
	 *
	 * @param CBHttpMessageRequest $message
	 */
	public function fromHttpRequestMessage(CBHttpMessageRequest $message);
}