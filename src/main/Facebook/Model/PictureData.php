<?php
/*
 * https://developers.facebook.com/docs/graph-api/reference/user/picture/
 */

namespace Bogo\Facebook\Model;

class PictureData extends Object
{
	/**
	 * The URL of the profile photo..
	 *
	 * @var string
	 */
	public $url;
	/**
	 * Indicates whether the profile photo is the default 'silhouette' picture, or has been replaced.
	 *
	 * @var boolean
	 */
	public $is_silhouette;

	/**
	 * Picture height in pixels.
	 *
	 * Height and width are only returned when specified as modifiers.
	 *
	 * @var integer
	 */
	public $height;

	/**
	 * Picture width in pixels.
	 *
	 * Height and width are only returned when specified as modifiers.
	 *
	 * @var integer
	 */
	public $width;
}