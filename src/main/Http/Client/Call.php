<?php
/*
 */

namespace Bogo\Http\Client;
use Bogo\Http\Message;

/**
 * Base HTTP Call.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Call
{
	/**
	 * Call created but not executed.
	 */
	const STATE_CREATED = 1;

	/**
	 * Call is currently being executed.
	 */
	const STATE_RUNNING = 2;

	/**
	 * Execution completed.
	 */
	const STATE_COMPLETED = 3;

	/**
	 * Text representation of states.
	 *
	 * @var string[]
	 */
	static public $stateTitles = array(
		self::STATE_CREATED => 'Created',
		self::STATE_RUNNING => 'Running',
		self::STATE_COMPLETED => 'Completed',
	);

	/**
	 * Total number of calls executed.
	 *
	 * @var integer
	 */
	static public $totalCallCount = 0;

	/**
	 * Total time consumed in call execution.
	 *
	 * @var float
	 */
	static public $totalExecutionSeconds = 0.0;

	/**
	 * Microsecond stamp of log initialization (roughly equal to request execution).
	 *
	 * @var float
	 */
	private $startMicroStamp = 0.0;

	/**
	 * Execution time in seconds.
	 *
	 * @var integer
	 */
	private $executionSeconds = 0.0;

	/**
	 * Timeout in seconds.
	 *
	 * @var float
	 */
	protected $timeoutSeconds = 1.0;

	/**
	 * Current state.
	 *
	 * @var integer
	 */
	private $state = self::STATE_CREATED;

	/**
	 * Code of execution error.
	 *
	 * @var integer
	 */
	private $errorCode = 0;

	/**
	 * Message of execution error.
	 *
	 * @var string
	 */
	private $errorMessage = '';

	/**
	 * Request message.
	 *
	 * @var Message\Request
	 */
	private $requestMessage = null;

	/**
	 * Response message.
	 *
	 * @var Message\Response
	 */
	private $responseMessage = null;

	/**
	 * Debug info filled after execution.
	 *
	 * @var mixed
	 */
	private $debugInfo = null;

	/**
	 * Code of execution error.
	 *
	 * @param integer $errorCode
	 */
	public function setErrorCode($errorCode)
	{
		$this->errorCode = $errorCode;
	}

	/**
	 * Message of execution error.
	 *
	 * @param string $errorMessage
	 */
	public function setErrorMessage($errorMessage)
	{
		$this->errorMessage = $errorMessage;
	}

	/**
	 * Code of execution error.
	 *
	 * @return integer
	 */
	public function getErrorCode()
	{
		return $this->errorCode;
	}

	/**
	 * Message of execution error.
	 *
	 * @return string
	 */
	public function getErrorMessage()
	{
		return $this->errorMessage;
	}

	/**
	 * Request message.
	 *
	 * @return Message\Request
	 */
	public function getRequestMessage()
	{
		return $this->requestMessage;
	}

	/**
	 * Response message.
	 *
	 * @return Message\Response
	 */
	public function getResponseMessage()
	{
		return $this->responseMessage;
	}

	/**
	 * Timeout in seconds.
	 *
	 * @return float
	 */
	public function getTimeoutSeconds()
	{
		return $this->timeoutSeconds;
	}

	/**
	 * Timeout in seconds.
	 *
	 * @param float $timeoutSeconds
	 * @return Call
	 */
	public function setTimeoutSeconds($timeoutSeconds)
	{
		$this->timeoutSeconds = $timeoutSeconds;

		return $this;
	}

	/**
	 * Execution duration in seconds.
	 *
	 * @return float
	 */
	public function getExecutionSeconds()
	{
		return $this->executionSeconds;
	}

	/**
	 * Call start stamp.
	 *
	 * @return float
	 */
	public function getStartMicroStamp()
	{
		return $this->startMicroStamp;
	}

	/**
	 * Start the execution timer.
	 */
	private function startTimer()
	{
		// Keep statistics
		self::$totalCallCount++;

		// Rough execution time
		$this->startMicroStamp = microtime(true);
	}

	/**
	 * Stop the execution timer.
	 */
	private function stopTimer()
	{
		// Measure how many seconds it took to execute
		$this->executionSeconds = microtime(true) - $this->startMicroStamp;

		// Keep statistics
		self::$totalExecutionSeconds += $this->executionSeconds;
	}

	/**
	 * Change internal state of call.
	 *
	 * @param integer $newState
	 */
	public function setState($newState)
	{
		switch ($newState) {
		case self::STATE_RUNNING:
			if ($this->state != self::STATE_CREATED) {
				throw new Exception('Cannot get to '.self::$stateTitles[$newState].' from '.self::$stateTitles[$this->state]);
			}
			$this->startTimer();
			break;

		case self::STATE_COMPLETED:
			if ($this->state != self::STATE_RUNNING) {
				throw new Exception('Cannot get to '.self::$stateTitles[$newState].' from '.self::$stateTitles[$this->state]);
			}
			$this->stopTimer();
			break;
		}

		// Everythin ok, update state
		$this->state = $newState;
	}

	/**
	 * Current call state.
	 *
	 * @return integer
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * Construct with request and response message.
	 *
	 * @param Message\Request $requestMessage
	 * @param Message\Response $responseMessage
	 */
	public function __construct(Message\Request $requestMessage, Message\Response $responseMessage = null)
	{
		$this->requestMessage = $requestMessage;
		$this->responseMessage = $responseMessage ?: new Message\Response();
	}

	/**
	 * Instantiate a cURL HTTP call.
	 *
	 * @param Message\Request|string $requestMessageOrVerb Request message or request verb
	 * @param string $requestUri
	 * @return Call
	 */
	static public function create($requestMessageOrVerb, $requestUri = null)
	{
		// Determine request message
		if ($requestMessageOrVerb instanceof Message\Request) {
			$requestMessage = $requestMessageOrVerb;
		} else {
			$requestMessage = Message\Request::create($requestMessageOrVerb, $requestUri);
		}

		// Instantiate call object
		$httpCallClassName = get_called_class();
		return new $httpCallClassName($requestMessage);
	}

	/**
	 * Returns extra debug info about call.
	 *
	 * The return value is vendor specific.
	 *
	 * @return mixed
	 */
	public function getDebugInfo()
	{
		return $this->debugInfo;
	}

	public function setDebugInfo($debugInfo)
	{
		return $this->debugInfo = $debugInfo;
	}

	/**
	 * Returns true if call is completed and failed.
	 *
	 * If a call has failed you can call getErrorCode() and getErrorMessage() for error details.
	 *
	 * @return boolean
	 */
	public function getHasFailed()
	{
		return ($this->errorCode != 0);
	}
}
