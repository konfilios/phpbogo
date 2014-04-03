<?php
/**
 * Repository handling queued http requests.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IBQueuedHttpRequestRepository
{
	/**
	 * Create and enqueue a new http request.
	 *
	 * @param integer $familyId
	 * @param CBHttpMessageRequest $message
	 * @return IBQueuedHttpRequest
	 */
	public function enqueue($familyId, CBHttpMessageRequest $message);

	/**
	 * Reserves a batch of IBQueuedHttpRequest instances for processing.
	 *
	 * @param integer $sessionId Consumer session reserving the batch.
	 * @param integer $maxBatchSize Max number of instances to reserve.
	 * @return IBQueuedHttpRequest[]
	 */
	public function reserveBatch($sessionId, $maxBatchSize = 1);

	/**
	 * Dequeue a request as successful.
	 *
	 * A request must have been reserved by the same session before being dequeued.
	 *
	 * @param integer $sessionId Consumer session dequeuing the request.
	 * @param IBQueuedHttpRequest $queuedHttpRequest Request being dequeued.
	 */
	public function dequeueSuccessful($sessionId, IBQueuedHttpRequest $queuedHttpRequest);

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
	public function dequeueFailed($sessionId, IBQueuedHttpRequest $queuedHttpRequest, $errorCode, $errorText);

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
	public function releaseRescheduled($sessionId, IBQueuedHttpRequest $queuedHttpRequest, $errorCode, $errorText, $nextAttemptStamp);

	/**
	 * Release requests reserved by consumer sessions that have expired.
	 *
	 * @param integer $sessionId Consumer session garbage collecting.
	 */
	public function releaseByExpiredSessionIds(array $expiredSessionIds);
}