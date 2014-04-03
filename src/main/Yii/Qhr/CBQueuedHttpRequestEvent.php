<?php
/**
 * CBQueuedHttpRequest event.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBQueuedHttpRequestEvent extends CBHttpCallEvent
{
	/**
	 * Queued http request this event is about.
	 *
	 * @var IBQueuedHttpRequest
	 */
	public $queuedHttpRequest;

	public function __construct($sender = null, $params = null, $call = null, $queuedHttpRequest)
	{
		parent::__construct($sender, $params, $call);
		$this->queuedHttpRequest = $queuedHttpRequest;
	}
}
