<?php
/**
 * Manages processes that span accross multiple calls.
 *
 * Processes belong to a single user session, and are thus stored in $_SESSION.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBSessionProcessManager extends CApplicationComponent
{
	const STATE_FUTURE = 1;
	const STATE_RUNNING = 2;
	const STATE_COMPLETED = 3;

	/**
	 * Name of process manager.
	 *
	 * This name is used as key in $_SESSION to store process states and queues.
	 *
	 * @var string
	 */
	public $name = 'spm';

	/**
	 * Registered process ids.
	 *
	 * @var string[]
	 */
	public $processIds;

	/**
	 * Restore or init process states and queues.
	 */
	public function init()
	{
		parent::init();

		foreach ($this->processIds as $processId) {
			if (!isset($_SESSION[$this->name.':'.$processId])) {
				$_SESSION[$this->name.':'.$processId] = array(
					'state' => self::STATE_FUTURE,
					'queue' => array(),
				);
			}
		}
	}

	/**
	 * Mark a process as "RUNNING".
	 *
	 * Only "FUTURE" processes can switch to "RUNNING" state.
	 *
	 * @param string $processId
	 */
	public function beginProcess($processId)
	{
		$key = $this->name.':'.$processId;

		if (empty($_SESSION[$key])) {
			throw new CException('Unregistered process '.$processId);
		}

		switch ($_SESSION[$key]['state']) {
		case self::STATE_FUTURE:
			$_SESSION[$key]['state'] = self::STATE_RUNNING;
			break;

		case self::STATE_RUNNING:
			throw new CException('Process '.$processId.' is already running');

		case self::STATE_COMPLETED:
			throw new CException('Process '.$processId.' is already completed');
		}

	}

	/**
	 * Mark a process as "COMPLETED".
	 *
	 * Only "RUNNING" processes can switch to "COMPLETED" state.
	 *
	 * Upon completion, all queued calls for this process are executed.
	 *
	 * @param string $processId
	 */
	public function completeProcess($processId)
	{
		$key = $this->name.':'.$processId;

		if (empty($_SESSION[$key])) {
			throw new CException('Unregistered process '.$processId);
		}

		switch ($_SESSION[$key]['state']) {
		case self::STATE_FUTURE:
			throw new CException('Process '.$processId.' never started');

		case self::STATE_RUNNING:
			$_SESSION[$key]['state'] = self::STATE_COMPLETED;
			break;

		case self::STATE_COMPLETED:
			throw new CException('Process '.$processId.' is already completed');
		}

		// Run all queued calls
		while (count($_SESSION[$key]['queue'])) {
			// Extract call from queue for ever
			$call = array_shift($_SESSION[$key]['queue']);
			// Execute
			$this->executeCall($processId, $call['methodName'], $call['callbackData']);
		}
	}

	/**
	 * Execute method when process is completed.
	 *
	 * If process is already completed, method is instantly executed. If not, it's queued for
	 * execution by completeProcess().
	 *
	 * @param string $processId
	 * @param string $methodName
	 * @param mixed $callbackData
	 */
	public function whenCompleted($processId, $methodName, $callbackData = null)
	{
		// Validate method name
		if (!method_exists($this, $methodName)) {
			throw new CException(get_class($this).' does not have a method called '.$methodName);
		}

		$key = $this->name.':'.$processId;

		if ($_SESSION[$key]['state'] == self::STATE_COMPLETED) {
			// Process is already complete, execute immediately
			$this->executeCall($processId, $methodName, $callbackData);
			return;
		}

		// Process is not completed yet, defer execution of method
		$_SESSION[$key]['queue'][] = array(
			'methodName' => $methodName,
			'callbackData' => $callbackData
		);
	}

	/**
	 * Executes $methodName because of $processId completion.
	 *
	 * @param string $processId
	 * @param string $methodName
	 * @param mixed $callbackData
	 */
	private function executeCall($processId, $methodName, $callbackData)
	{
		call_user_func(array($this, $methodName), $callbackData);
	}
}
