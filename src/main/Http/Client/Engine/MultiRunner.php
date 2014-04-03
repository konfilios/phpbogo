<?php
/*
 */

namespace Bogo\Http\Client\Engine;

/**
 * Multi-call execution.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
abstract class MultiRunner
{
	/**
	 * List of calls to be executed.
	 *
	 * @var Call[]
	 */
	private $calls = array();

	/**
	 * Error code.
	 *
	 * @var integer
	 */
	protected $errorCode = 0;

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
	 * Start the execution timer.
	 */
	protected function startTimer()
	{
		// Rough execution time
		$this->startMicroStamp = microtime(true);
	}

	/**
	 * Stop the execution timer.
	 */
	protected function stopTimer()
	{
		// Measure how many seconds it took to execute
		$this->executionSeconds = microtime(true) - $this->startMicroStamp;
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
	 * Error code.
	 *
	 * @return integer
	 */
	public function getErrorCode()
	{
		return $this->errorCode;
	}

	/**
	 * Set a call with a given key.
	 *
	 * @param string $key
	 * @param Call $call
	 */
	protected function setCall($key, SingleRunner $call = null)
	{
		if ($call === null) {
			if (isset($this->calls[$key])) {
				unset($this->calls[$key]);
			}
		} else {
			$this->calls[$key] = $call;
		}
	}

	/**
	 * List of calls to be executed.
	 *
	 * @return Call[]
	 */
	public function getCalls()
	{
		return $this->calls;
	}

	/**
	 * Retrieve response messages of all calls.
	 *
	 * @return Message\Response[]
	 */
	public function getResponseMessages()
	{
		$responseMessages = array();
		foreach ($this->calls as $key=>$call) {
			/* @var $call Call */
			$responseMessages[$key] = $call->getResponseMessage();
		}
		return $responseMessages;
	}

	/**
	 * Execute all calls.
	 *
	 * @return MultiRunner
	 */
	abstract public function run();
}
