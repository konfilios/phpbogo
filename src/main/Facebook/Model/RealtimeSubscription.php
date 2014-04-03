<?php
/*
 * https://developers.facebook.com/docs/graph-api/reference/app/subscriptions
 */

namespace Bogo\Facebook\Model;

class RealtimeSubscription extends Object
{
	/**
	 * Indicates the object type that this subscription applies to.
	 *
	 * Valid values:
	 * <ul>
	 * <li><b>user</b></li>
	 * <li><b>page</b></li>
	 * <li><b>errors</b></li>
	 * <li><b>permissions</b></li>
	 * <li><b>payments</b></li>
	 * <li><b>payment_subscriptions</b></li>
	 * </ul>
	 *
	 * @var string
	 */
	public $object;

	/**
	 * The URL that will receive the POST request when an update is triggered..
	 *
	 * @var string
	 */
	public $callback_url;

	/**
	 * The set of fields in this object that are subscribed to.
	 *
	 * @var string[]
	 */
	public $fields;

	/**
	 * Indicates whether or not the subscription is active.
	 *
	 * @var boolean
	 */
	public $active;
}
