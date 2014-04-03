<?php
/*
 * https://developers.facebook.com/docs/reference/api/post/
 */


namespace Bogo\Facebook\Model;

/**
 *
 */
class Post extends Object
{
	const TYPE_STATUS = "status";
	const TYPE_CHECKIN = "checkin";

	public function attributeTypes()
	{
		return array(
			'from' => 'Bogo\Facebook\Model\User',
			'to' => 'Bogo\Facebook\Model\UserCollection',
			'tags' => 'Bogo\Facebook\Model\UserCollection',
			'with_tags' => 'Bogo\Facebook\Model\UserCollection',
//			'message_tags' => 'Bogo\Facebook\Model\MessageTag[]',
			'actions' => 'Bogo\Facebook\Model\Action[]',
			'privacy' => 'Bogo\Facebook\Model\PrivacyParameter',
			'place' => 'Bogo\Facebook\Model\Place',
			'application' => 'Bogo\Facebook\Model\Application',
			'likes' => 'Bogo\Facebook\Model\UserTagCollection',
			'comments' => 'Bogo\Facebook\Model\CommentCollection',
		);
	}

	/**
	 * The post ID.
	 *
	 * A pair of big integers concatenated with an underscore.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Information about the user who posted the message.
	 *
	 * @var User
	 */
	public $from;

	/**
	 * Profiles mentioned or targeted in this post.
	 *
	 * @var UserCollection
	 */
	public $to;

	/**
	 * Objects (Users, Pages, etc) tagged as being with the publisher of the post ('Who are you with?' on Facebook).
	 *
	 * @permissions read_stream
	 * @var UserCollection
	 */
	public $with_tags;

	/**
	 * Similar to 'with_tags' but only appears when called from user/locations.
	 *
	 * @permissions read_stream
	 * @var UserCollection
	 */
	public $tags;

	/**
	 * The message.
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
	 * If available, a link to the picture included with this post.
	 *
	 * @var string
	 */
	public $picture;

	/**
	 * The link attached to this post.
	 *
	 * @var string
	 */
	public $link;

	/**
	 * The name of the link.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The caption of the link (appears beneath the link name).
	 *
	 * @var string
	 */
	public $caption;

	/**
	 * A list of available actions on the post (including commenting, liking, and an optional app-specified action).
	 *
	 * @var Action[]
	 */
	public $actions;

	/**
	 * The privacy settings of the Post.
	 *
	 * @var PrivacyParameter
	 */
	public $privacy;

	/**
	 * Location associated with a Post, if any.
	 *
	 * @permissions read_stream
	 * @var Place
	 */
	public $place;

	/**
	 * A string indicating the type for this post (including link, photo, video).
	 *
	 * @var string
	 */
	public $type;

	/**
	 * A link to an icon representing the type of this post.
	 *
	 * @var string
	 */
	public $icon;

	/**
	 * Information about the application this post came from.
	 *
	 * @var Application
	 */
	public $application;

	/**
	 * The likes on this post.
	 *
	 * @var UserTagCollection
	 */
	public $likes;

	/**
	 * All of the comments on this post.
	 *
	 * @var CommentCollection
	 */
	public $comments;

	/**
	 * The time the post was initially published.
	 *
	 * @var utc8601datetime
	 */
	public $created_time;

	/**
	 * The time of the last comment on this post.
	 *
	 * @var utc8601datetime
	 */
	public $updated_time;
}