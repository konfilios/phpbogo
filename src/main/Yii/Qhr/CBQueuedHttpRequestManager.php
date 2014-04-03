<?php
/**
 * Queued Http Request Manager.
 *
 * Coordinates consumers and producers, providing common utilities.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBQueuedHttpRequestManager extends CApplicationComponent
{
	/**
	 * Class to instantiate producer singleton.
	 *
	 * @var string
	 */
	public $producerClass = 'CBQueuedHttpRequestProducer';

	/**
	 * Queue name.
	 *
	 * Helpful if you want to maintain multiple queues for some reason.
	 *
	 * @var string
	 */
	public $queueName = 'HttpQueue';

	/**
	 * Class to instantiate consumer singleton.
	 *
	 * @var string
	 */
	public $consumerClass = 'CBQueuedHttpRequestConsumer';

	/**
	 * Class to instantiate consumer executor.
	 *
	 * @var string
	 * @todo Executor details should not be defined here.
	 */
	public $httpCallExecutorClass = 'CBHttpCallExecutorBuffered';

	/**
	 * Capacity of buffered http executor.
	 * @var integer
	 */
	public $httpCallExecutorCapacity = 20;

	/**
	 * Redis guard specification
	 * @var mixed
	 */
//	public $redisGuard = array(
//		'class' => 'ARedisGuard',
//		'connectionId' => 'redis',
//		'blockTimeoutSeconds' => 25,
//	);

	/**
	 * Http repository class.
	 *
	 * @var string
	 */
	public $queuedRequestRepositoryClass = 'QueuedHttpRequest';

	/**
	 * Http repository class.
	 *
	 * @var string
	 */
	public $consumerSessionRepositoryClass = 'CBDummyQueuedHttpRequestConsumerSessionRepository';

	/**
	 * Code guard for facebook push process.
	 *
	 * @var ARedisGuard
	 */
	private $redisGuard = null;

	/**
	 * Consumer singleton.
	 *
	 * @var CBQueuedHttpRequestConsumer
	 */
	private $consumer = null;

	/**
	 * Producer singleton.
	 *
	 * @var CBQueuedHttpRequestProducer
	 */
	private $producer = null;

	/**
	 * Queued request repository singleton.
	 *
	 * @var IBQueuedHttpRequestRepository
	 */
	private $queuedRequestRepository = null;

	/**
	 * Consumer session repository singleton.
	 *
	 * @var IBQueuedHttpRequestConsumerSessionRepository
	 */
	private $consumerSessionRepository = null;

	/**
	 * Returns facebook push code block guard.
	 *
	 * @var mixed $options
	 * @return ARedisGuard
	 */
	public function setRedisGuard($options)
	{
		if (is_array($options)) {
			// Parameter is array of configuration options for ad hoc construction of a redis guard.
			$guardClass = $options['class'];
			$redisConnectionId = $options['connectionId'];

			$this->redisGuard = new $guardClass($this->queueName.'Guard', Yii::app()->$redisConnectionId);
			$this->redisGuard->blockTimeout = $options['blockTimeoutSeconds'];

		} else if (is_string($options)) {
			// Parameter if the id of a yii component
			$this->redisGuard = Yii::app()->$options;

		} else {
			// Parameter is a redis guard object already
			$this->redisGuard = $options;
		}
	}

	/**
	 * Wait for producers to produce requests.
	 *
	 * @param float $timeoutSeconds
	 * @return boolean True if consumer should look for new queued requests.
	 */
	public function waitForProducers($timeoutSeconds)
	{
		if ($this->redisGuard !== null) {
			return $this->redisGuard->wait();
		} else {
			usleep($timeoutSeconds * 1000000.0);
			return true;
		}
	}

	/**
	 * Notify consumers requests have been produced.
	 */
	public function notifyConsumers()
	{
		if ($this->redisGuard !== null) {
			$this->redisGuard->notify();
		} else {
			// Nothing to do, consumers poll for new requests
		}
	}

	/**
	 * Consumer singleton.
	 *
	 * @return CBQueuedHttpRequestConsumer
	 */
	public function getConsumer()
	{
		if ($this->consumer === null) {
			// Create executor first
			$httpCallExecutorClass = $this->httpCallExecutorClass;
			$httpCallExecutor = new $httpCallExecutorClass($this->httpCallExecutorCapacity);

			// Create consumer
			$consumerClass = $this->consumerClass;
			$this->consumer = new $consumerClass($this, $httpCallExecutor);
		}

		return $this->consumer;
	}

	/**
	 * Producer singleton.
	 *
	 * @return CBQueuedHttpRequestProducer
	 */
	public function getProducer()
	{
		if ($this->producer === null) {
			$producerClass = $this->producerClass;
			$this->producer = new $producerClass($this);
		}

		return $this->producer;
	}

	/**
	 * Queued request repository singleton.
	 *
	 * @return IBQueuedHttpRequestRepository
	 */
	public function getQueuedRequestRepository()
	{
		if ($this->queuedRequestRepository === null) {
			$queuedRequestRepositoryClass = $this->queuedRequestRepositoryClass;
			$this->queuedRequestRepository = new $queuedRequestRepositoryClass();
		}

		return $this->queuedRequestRepository;
	}

	/**
	 * Consumer session repository singleton.
	 *
	 * @return IBQueuedHttpRequestConsumerSessionRepository
	 */
	public function getConsumerSessionRepository()
	{
		if ($this->consumerSessionRepository === null) {
			$consumerSessionRepositoryClass = $this->consumerSessionRepositoryClass;
			$this->consumerSessionRepository = new $consumerSessionRepositoryClass();
		}

		return $this->consumerSessionRepository;
	}
}
