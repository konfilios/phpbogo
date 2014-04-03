<?php
/*
 * https://developers.facebook.com/docs/reference/api/privacy-parameter/
 */

namespace Bogo\Facebook\Model;
use Bogo\Yii\Webservice;

class PrivacyParameter extends Object
{
	const VALUE_EVERYONE = "EVERYONE";
	const VALUE_ALL_FRIENDS = "ALL_FRIENDS";
	const VALUE_FRIENDS_OF_FRIENDS = "FRIENDS_OF_FRIENDS";
	const VALUE_CUSTOM = "CUSTOM";
	const VALUE_SELF = "SELF";

	/**
	 * The privacy value for the object (post, photo, album, etc).
	 *
	 * Allowed values are enumerated as VALUE_* constants in this class.
	 *
	 * @var string
	 */
	public $value;

	/**
	 * For CUSTOM settings, a comma-separated list of user IDs and friend list IDs that can see the post.
	 *
	 * This can also be ALL_FRIENDS or FRIENDS_OF_FRIENDS to include all members of those sets.
	 *
	 * @var string
	 */
	public $allow;

	/**
	 * For CUSTOM settings, a comma-separated list of user IDs and friend list IDs that cannot see the post.
	 *
	 * @var string
	 */
	public $deny;

	/**
	 * Privacy description.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * This entire parameter has been deprecated.
	 *
	 * Previous values were FRIENDS_OF_FRIENDS, ALL_FRIENDS, or SOME_FRIENDS and were only used when value was set to CUSTOM.
	 * 
	 * @deprecated
	 * @var string
	 */
	public $friends;

	/**
	 * This parameter has been removed and is no longer supported.
	 *
	 * @deprecated
	 * @var string
	 */
	public $networks;
}