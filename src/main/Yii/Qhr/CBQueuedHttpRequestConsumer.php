<?php
/**
 * Queued http request consumer.
 *
 * This components utilizes the following resources to automate queued http request processing:
 * <ul>
 * <li>A CBQueuedHttpRequestManager which coordinates consumers and producers.</li>
 * <li>A CBHttpCallExecutor which actually executes the https calls</li>
 * </ul>
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBQueuedHttpRequestConsumer extends CComponent
{
	/**
	 * Maximum number of request retries before failing for ever.
	 *
	 * @var integer
	 */
	public $maxRetries = 1;

	/**
	 * Reschedule re-triable request after given amount of time.
	 *
	 * @var integer
	 */
	public $rescheduleAfterSeconds = 3600;

	/**
	 * Default http call execution timeout used by createHttpCall().
	 *
	 * @var integer
	 */
	public $defaultCallTimeoutSeconds = 2;

	/**
	 * Http call executor.
	 *
	 * @var CBHttpCallExecutor
	 */
	private $httpCallExecutor;

	/**
	 * Parent manager.
	 *
	 * @var CBQueuedHttpRequestManager
	 */
	private $manager;

	/**
	 * Iterator of consumable http requests.
	 *
	 * @var IBQueuedHttpRequest[]
	 */
	private $reservedRequests;

	/**
	 * Currently active session id.
	 *
	 * @var integer
	 */
	private $sessionId;

	/**
	 * Expiration timestamp of active session id.
	 * @var integer
	 */
	private $sessionExpirationStamp;

	/**
	 * Instantiate consumer.
	 *
	 * @param CBQueuedHttpRequestManager $manager
	 */
	public function __construct(CBQueuedHttpRequestManager $manager, CBHttpCallExecutor $httpCallExecutor)
	{
		$this->manager = $manager;
		$this->httpCallExecutor = $httpCallExecutor;
	}

	/**
	 * Http call executor.
	 *
	 * @return CBHttpCallExecutor
	 */
	public function getHttpCallExecutor()
	{
		return $this->httpCallExecutor;
	}

	private function hasLiveSession()
	{
		if ($this->sessionId === null) {
			return false;
		}

		if ($this->sessionExpirationStamp <= time()) {
			return false;
		}

		return true;
	}

	/**
	 * Begin a new consumer session.
	 *
	 * @param integer $sessionLifetime Max session lifetime in seconds.
	 * @throws CException
	 */
	private function beginSession($sessionLifetime)
	{
		$this->sessionExpirationStamp = time() + ceil($sessionLifetime);
		$this->sessionId = $this->manager->getConsumerSessionRepository()->createSession($this->sessionExpirationStamp);
	}

	/**
	 * Refresh existing consumer session.
	 *
	 * @param integer $sessionLifetime Max session lifetime in seconds.
	 */
	private function refreshSession($sessionLifetime)
	{
		$newExpirationStamp = time() + ceil($sessionLifetime);

		if ($newExpirationStamp > $this->sessionExpirationStamp) {
			$this->sessionExpirationStamp = $newExpirationStamp;
			$this->manager->getConsumerSessionRepository()->refreshSession($this->sessionId, $this->sessionExpirationStamp);
		}
	}

	/**
	 * Wait for consumable requests.
	 *
	 * @param type $waitTimeoutSeconds Timeout for wait().
	 * @param type $maxBatchSize Maximum number of queued requests to reserve.
	 * @return integer Number of requests pending for consumption.
	 */
	public function waitForBatch($waitTimeoutSeconds)
	{
		// Make sure our session will last long enough
		if (!$this->hasLiveSession()) {
			$this->beginSession($waitTimeoutSeconds * 1.5);
		} else {
			$this->refreshSession($waitTimeoutSeconds * 1.5);
		}

		return ($this->manager->waitForProducers($waitTimeoutSeconds));
	}

	public function reserveBatch($maxBatchSize = 1)
	{
		// Something has been produced
		$this->reservedRequests = $this->manager->getQueuedRequestRepository()->reserveBatch($this->sessionId, $maxBatchSize);

		return count($this->reservedRequests);
	}

	/**
	 * Consume previously reserved batch.
	 *
	 * @param integer $consumeTimeoutSeconds Timeout of consumption process.
	 */
	public function consumeBatch($consumeTimeoutSeconds)
	{
		// Make sure our session is still live
		if (!$this->hasLiveSession()) {
			throw new CException('No valid session found');
		}
		$this->refreshSession($consumeTimeoutSeconds);

		$hasBeforeSubmitRequestHandler = $this->hasEventHandler('onBeforeSubmitRequest') ?  : null;

		foreach ($this->reservedRequests as $consumableHttpRequest) {
			/* @var $consumableHttpRequest IBQueuedHttpRequest */

			// Create a call out of the consumable http request
			$futureCall = $this->createHttpCall($consumableHttpRequest);

			// Make sure our session is still live
			if (!$this->hasLiveSession()) {
				throw new CException('Session expired');
			}

			// Notify listeners
			if ($hasBeforeSubmitRequestHandler) {
				$this->onBeforeSubmitRequest(new CBQueuedHttpRequestEvent($this, null, $futureCall, $consumableHttpRequest));
			}

			// Submit call and retrieve executed ones
			$executedCalls = $this->httpCallExecutor->submit($futureCall);

			// Make sure our session is still live
			if (!$this->hasLiveSession()) {
				throw new CException('Session expired');
			}

			if (!empty($executedCalls)) {
				$this->processCompletedCalls($executedCalls);
			}
		}

		// Flush remaining and mark successful as read
		$this->processCompletedCalls($this->httpCallExecutor->invokeAll());

		$this->reservedRequests = null;
	}

	/**
	 * Process completed http calls.
	 *
	 * This is a convenience method required by the CBHttpCallExecutor coding idiom. It extracts
	 * queued http request objects from calls and forwards them to consumeCompletedRequest()
	 * method where actual handling takes place.
	 *
	 * @param CBHttpCall[] $completedCalls
	 * @return integer Number of http requests removed from queue (consumed).
	 */
	private function processCompletedCalls($completedCalls)
	{
		$processedCount = 0;
		foreach ($completedCalls as $completedCall) {
			/* @var $completedCall CBHttpCall */

			// Extract queued request
			$queuedHttpRequest = $completedCall->getRequestMessage()->getUserField('queuedHttpRequest');
			/* @var $queuedHttpRequest IBQueuedHttpRequest */

			// Try to consume request
			if ($this->consumeCompletedRequest($queuedHttpRequest, $completedCall)) {
				// Request was consumed, notify listeners (if any)
				$this->afterConsumeRequest($queuedHttpRequest, $completedCall);
			}

			$processedCount++;
		}

		return $processedCount;
	}

	/**
	 * Post-process consumed request.
	 *
	 * @param IBQueuedHttpRequest $queuedHttpRequest
	 * @param CBHttpCall $completedCall
	 */
	protected function afterConsumeRequest(IBQueuedHttpRequest $queuedHttpRequest, CBHttpCall $completedCall)
	{
		if ($this->hasEventHandler('onAfterConsumeRequest')) {
			$this->onAfterConsumeRequest(new CBQueuedHttpRequestEvent($this, null, $completedCall, $queuedHttpRequest));
		}
	}

	/**
	 * Compose an executable CBHttpCall out of a IBQueuedHttpRequest.
	 *
	 * @param IBQueuedHttpRequest $queuedHttpRequest
	 * @return CBHttpCall
	 */
	protected function createHttpCall(IBQueuedHttpRequest $queuedHttpRequest)
	{
		return $queuedHttpRequest->toHttpRequestMessage()
				->setUserField('queuedHttpRequest', $queuedHttpRequest)
				->createCall()
				->setTimeoutSeconds($this->defaultCallTimeoutSeconds);
	}

	/**
	 * Validate completed request and throw an exception if should be considered failed.
	 *
	 * @param IBQueuedHttpRequest $queuedHttpRequest
	 * @param CBHttpCall $completedCall
	 * @throws CBHttpCallException
	 * @throws CHttpException
	 */
	protected function validateCompletedRequest(IBQueuedHttpRequest $queuedHttpRequest, CBHttpCall $completedCall)
	{
		// Scan for network errors
		if ($completedCall->getHasFailed()) {
			throw new CBHttpCallException($completedCall->getErrorMessage(), $completedCall->getErrorCode());
		}

		// Scan for HTTP errors
		$responseMessage = $completedCall->getResponseMessage();
		if (!$responseMessage->hasSuccessfulStatusCode()) {
			throw new CHttpException($responseMessage->getHttpStatusCode(), $responseMessage->getHttpReasonPhrase(), $responseMessage->getHttpStatusCode());
		}
	}

	/**
	 * Decide whether (and for when) a failed request should be re-scheduled.
	 *
	 * @param IBQueuedHttpRequest $queuedHttpRequest
	 * @param CBHttpCall $completedCall
	 * @param Exception $e
	 * @return integer|boolean A valid timestamp for rescheduling of false to mark as failed.
	 */
	protected function getFailedRequestRescheduleStamp(IBQueuedHttpRequest $queuedHttpRequest, CBHttpCall $completedCall, Exception $e)
	{
		if ($queuedHttpRequest->attemptCount >= $this->maxRetries) {
			return false;
		} else {
			return time() + $this->rescheduleAfterSeconds;
		}
	}

	/**
	 * Consumes a completed request.
	 *
	 * This is where the retry strategy and queue update takes place.
	 *
	 * The request must either be dequeued or rescheduled.
	 *
	 * @param IBQueuedHttpRequest $queuedHttpRequest
	 * @return boolean True if request was dequeued (consumed), false if it was rescheduled.
	 */
	protected function consumeCompletedRequest(IBQueuedHttpRequest $queuedHttpRequest, CBHttpCall $completedCall)
	{
		try {
			// Check if request was successful
			$this->validateCompletedRequest($queuedHttpRequest, $completedCall);

			// Success
			$this->manager->getQueuedRequestRepository()->dequeueSuccessful($this->sessionId, $queuedHttpRequest);
			return true;

		} catch (Exception $e) {
			$rescheduleStamp = $this->getFailedRequestRescheduleStamp($queuedHttpRequest, $completedCall, $e);

			// If this was the first attempt, retry
			if ($rescheduleStamp === false) {
				// Mark as failed
				$this->manager->getQueuedRequestRepository()->dequeueFailed($this->sessionId, $queuedHttpRequest, $e->getCode(), $e->getMessage());
				return true;

			} else {
				// Retry after two hours
				$this->manager->getQueuedRequestRepository()->releaseRescheduled($this->sessionId, $queuedHttpRequest, $e->getCode(), $e->getMessage(), $rescheduleStamp);
				return false;
			}
		}
	}

	/**
	 * Called after an individual call is completed.
	 *
	 * @param CEvent $event
	 */
	public function onBeforeSubmitRequest(CBQueuedHttpRequestEvent $event)
	{
		$this->raiseEvent('onBeforeSubmitRequest', $event);
	}

	/**
	 * Called after an individual call is completed.
	 *
	 * @param CEvent $event
	 */
	public function onAfterConsumeRequest(CBQueuedHttpRequestEvent $event)
	{
		$this->raiseEvent('onAfterConsumeRequest', $event);
	}
}