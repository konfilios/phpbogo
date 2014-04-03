<?php
/*
 */

namespace Bogo\Http\Client\Engine;
use Bogo\Http\Client;
use Bogo\Http\Message;

abstract class Base extends \CApplicationComponent
{
	/**
	 * Perform extra debugging tasks.
	 *
	 * @var boolean
	 */
	private $inDebugMode = false;

	/**
	 * Perform extra debugging tasks.
	 *
	 * @return boolean
	 */
	public function getInDebugMode()
	{
		return $this->inDebugMode;
	}

	/**
	 * Perform extra debugging tasks.
	 *
	 * @param boolean $inDebugMode
	 * @return Call
	 */
	public function setInDebugMode($inDebugMode)
	{
		$this->inDebugMode = $inDebugMode;

		return $this;
	}

	public function run($callOrRequest, $throwExceptionOnFailure = true)
	{
		if ($callOrRequest instanceof Message\Request) {
			$call = new Client\Call($callOrRequest);
		} else {
			$call = $callOrRequest;
		}

		return $this->createSingleCallRunner($call)->run($throwExceptionOnFailure);
	}

	/**
	 * @return SingleRunner
	 */
	abstract public function createSingleCallRunner(Client\Call $call);

	/**
	 * @return MultiRunner
	 */
	abstract public function createMultiCallRunner();
}
