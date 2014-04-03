<?php
/*
 */

namespace Bogo\Yii\Http\Client;

/**
 * Call event.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CallEvent extends CEvent
{
	/**
	 * Call this event is about.
	 *
	 * @var Call
	 */
	public $call;

	public function __construct($sender = null, $params = null, $call = null)
	{
		parent::__construct($sender, $params);
		$this->call = $call;
	}
}
