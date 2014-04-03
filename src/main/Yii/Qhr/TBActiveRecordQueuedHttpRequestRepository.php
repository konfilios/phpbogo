<?php
/**
 * A typical ActiveRecord implementation of IBQueuedHttpRequestRepository.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
trait TBActiveRecordQueuedHttpRequestRepository
{
	/**
	 * Reserves a batch of IBQueuedHttpRequest instances for processing.
	 *
	 * @param integer $sessionId Consumer session reserving the batch.
	 * @param integer $maxBatchSize Max number of instances to reserve.
	 * @return IBQueuedHttpRequest[]
	 */
	public function reserveBatch($sessionId, $maxBatchSize = 1)
	{
		$criteria = new CDbCriteria();

		$criteria->compare('stateId', IBQueuedHttpRequest::STATE_PENDING);
		$criteria->compare('nextAttemptUdatetime', '<='.gmdate('Y-m-d H:i:s'));
		$criteria->limit = $maxBatchSize;
		$criteria->order = 't.nextAttemptUdatetime, t.id';

		$queuedHttpRequestModel = new static();

		return $queuedHttpRequestModel->findAll($criteria);
	}

	/**
	 * Dequeue a request as successful.
	 *
	 * A request must have been reserved by the same session before being dequeued.
	 *
	 * @param integer $sessionId Consumer session dequeuing the request.
	 * @param IBQueuedHttpRequest $queuedHttpRequest Request being dequeued.
	 */
	public function dequeueSuccessful($sessionId, IBQueuedHttpRequest $queuedHttpRequest)
	{
		$queuedHttpRequest->logLastAttempt();
		$queuedHttpRequest->logLastError(null, null);
		$queuedHttpRequest->stateId = IBQueuedHttpRequest::STATE_SUCCESS;
		$queuedHttpRequest->nextAttemptUdatetime = null;
		$queuedHttpRequest->save();
	}

	/**
	 * Release and re-schedule an http request.
	 *
	 * A request must have been reserved by the same session before being released as rescheduled.
	 *
	 * @param integer $sessionId Consumer session dequeuing the request.
	 * @param IBQueuedHttpRequest $queuedHttpRequest Request being dequeued.
	 * @param type $errorCode Code of failure error.
	 * @param type $errorText Text of failure error.
	 * @param integer $nextAttempStamp Timestamp of next attempt.
	 */
	public function releaseRescheduled($sessionId, IBQueuedHttpRequest $queuedHttpRequest, $errorCode, $errorText, $nextAttemptStamp)
	{
		$queuedHttpRequest->logLastAttempt();
		$queuedHttpRequest->logLastError($errorCode, $errorText);
		$queuedHttpRequest->stateId = IBQueuedHttpRequest::STATE_PENDING;
		$queuedHttpRequest->nextAttemptUdatetime = gmdate('Y-m-d H:i:s', $nextAttemptStamp);
		$queuedHttpRequest->save();
	}

	/**
	 * Dequeue request as failed.
	 *
	 * A request must have been reserved by the same session before being dequeued.
	 *
	 * @param integer $sessionId Consumer session dequeuing the request.
	 * @param IBQueuedHttpRequest $queuedHttpRequest Request being dequeued.
	 * @param type $errorCode Code of failure error.
	 * @param type $errorText Text of failure error.
	 */
	public function dequeueFailed($sessionId, IBQueuedHttpRequest $queuedHttpRequest, $errorCode, $errorText)
	{
		$queuedHttpRequest->logLastAttempt();
		$queuedHttpRequest->logLastError($errorCode, $errorText);
		$queuedHttpRequest->stateId = IBQueuedHttpRequest::STATE_FAILED;
		$queuedHttpRequest->nextAttemptUdatetime = null;
		$queuedHttpRequest->save();
	}

	/**
	 * Create and enqueue a new http request.
	 *
	 * @param integer $familyId
	 * @param CBHttpMessageRequest $message
	 * @return IBQueuedHttpRequest
	 */
	public function enqueue($familyId, CBHttpMessageRequest $message)
	{
		$queuedHttpRequest = new static();

		$queuedHttpRequest->fromHttpRequestMessage($message);
		$queuedHttpRequest->familyId = $familyId;

		$queuedHttpRequest->stateId = IBQueuedHttpRequest::STATE_PENDING;
		$queuedHttpRequest->attemptCount = 0;

		$queuedHttpRequest->createUdatetime = gmdate('Y-m-d H:i:s');
		$queuedHttpRequest->nextAttemptUdatetime = gmdate('Y-m-d H:i:s');

		$queuedHttpRequest->save();

		return $queuedHttpRequest;
	}

	/**
	 * Release requests reserved by consumer sessions that have expired.
	 *
	 * @param integer $sessionId Consumer session garbage collecting.
	 */
	public function releaseByExpiredSessionIds(array $expiredSessionIds)
	{
	}
}