<?php
/*
 * https://developers.facebook.com/docs/graph-api/real-time-updates/
 */

namespace Bogo\Facebook\Model;

class RealtimeSubscriptionVerification extends Object
{
	/**
	 * The string "subscribe" is passed in this parameter
	 *
	 * @var string
	 */
	public $hub_mode;

	/**
	 * A random string that must be echoed back to facebook.
	 *
	 * @var string
	 */
	public $hub_challenge;

	/**
	 * The verify_token value you specified when you created the subscription.
	 * 
	 * @var string
	 */
	public $hub_verify_token;
}
