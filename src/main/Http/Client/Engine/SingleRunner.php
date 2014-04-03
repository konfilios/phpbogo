<?php
/*
 */

namespace Bogo\Http\Client\Engine;
use Bogo\Http\Message;

abstract class SingleRunner
{
	/**
	 * Executes request message and returns response message.
	 *
	 * @param boolean $throwExceptionOnFailure If true an exception is thrown on network error.
	 * @return Message\Response Response message or null if a network error occured.
	 */
	abstract public function run($throwExceptionOnFailure = false);
}
