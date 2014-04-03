<?php
/**
 * Produces http requests.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBQueuedHttpRequestProducer extends CComponent
{
	/**
	 * Defered consumer notification flag.
	 *
	 * @var boolean
	 */
	private $shouldNotifyConsumers = false;

	/**
	 * Parent manager.
	 *
	 * @var CBQueuedHttpRequestManager
	 */
	private $manager;

	/**
	 * Instantiate producer.
	 *
	 * @param CBQueuedHttpRequestManager $manager
	 */
	public function __construct(CBQueuedHttpRequestManager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * Produce a new queued http request.
	 *
	 * Push the request in the queue and defer consumer notification.
	 *
	 * @param integer $familyId
	 * @param CBHttpMessageRequest $message
	 * @return IBQueuedHttpRequest
	 */
	public function produceRequest($familyId, CBHttpMessageRequest $message)
	{
		$queuedHttpRequest = $this->manager->getQueuedRequestRepository()->enqueue($familyId, $message);

		$this->shouldNotifyConsumers = true;

		return $queuedHttpRequest;
	}

	/**
	 * Notify consumers to consume produced requests.
	 */
	public function notifyConsumers()
	{
		$this->manager->notifyConsumers();
		$this->shouldNotifyConsumers;
	}

	/**
	 * Execute deferred consumer notification if necessary.
	 */
	public function __destruct()
	{
		if ($this->shouldNotifyConsumers) {
			$this->notifyConsumers();
		}
	}
}