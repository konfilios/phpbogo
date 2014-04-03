<?php
/*
 * https://developers.facebook.com/docs/reference/api/comment
 */

namespace Bogo\Facebook\Model;

class Comment extends Object
{
	/**
	 * The Facebook ID of the comment.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * The user that created the comment.
	 *
	 * @var UserTag
	 */
	public $from;

	/**
	 * The comment text.
	 *
	 * @var string
	 */
	public $message;

	/**
	 * Objects tagged in the message (Users, Pages, etc).
	 *
	 * @var array[]
	 */
	public $message_tags;

	/**
	 * Specifies whether you can remove this comment.
	 *
	 * @var boolean
	 */
	public $can_remove;

	/**
	 * The number of times this comment was liked.
	 *
	 * @var integer
	 */
	public $like_count;

	/**
	 * This field is returned only if the authenticated user likes this comment.
	 *
	 * @var boolean
	 */
	public $user_likes;

	/**
	 * The timedate the comment was created.
	 *
	 * @var utc8601datetime
	 */
	public $created_time;
}