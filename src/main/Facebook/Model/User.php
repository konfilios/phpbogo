<?php
/*
 * https://developers.facebook.com/docs/graph-api/reference/user
 */

namespace Bogo\Facebook\Model;

class User extends UserTag
{
	const GENDER_MALE = "male";
	const GENDER_FEMALE = "female";

	const REL_STATUS_SINGLE = 'Single';
	const REL_STATUS_IN_RELATIONSHIP = 'In a relationship';
	const REL_STATUS_ENGAGED = 'Engaged';
	const REL_STATUS_MARRIED = 'Married';
	const REL_STATUS_IN_OPEN_RELATIONSHIP = 'In an open relationship';
	const REL_STATUS_COMPLICATED = 'It\'s complicated';
	const REL_STATUS_SEPARATED = 'Separated';
	const REL_STATUS_DIVORCED = 'Divorced';
	const REL_STATUS_WIDOWED = 'Widowed';

	public function attributeTypes()
	{
		return array(
			'picture' => 'Bogo\Facebook\Model\Picture',
		);
	}

	/**
	 * The person's username.
	 *
	 * @var string
	 */
	public $username;

	/**
	 * This person's primary email address listed on their profile.
	 *
	 * @var string
	 */
	public $email;

	/**
	 * The person's first name.
	 *
	 * @var string
	 */
	public $first_name;

	/**
	 * The person's last name.
	 *
	 * @var string
	 */
	public $last_name;

	/**
	 * The person's middle name.
	 *
	 * @var $middle_name;
	 */
	public $middle_name;

	/**
	 * This person's birthday in the format MM/DD/YYYY.
	 *
	 * @var string
	 */
	public $birthday;

	/**
	 * The person's gender.
	 *
	 * @var string
	 */
	public $gender;

	/**
	 * A link to the person's profile.
	 *
	 * @var string
	 */
	public $link;

	/**
	 * The person's locale.
	 *
	 * @var string
	 */
	public $locale;

	/**
	 * The person's profile picture.
	 *
	 * @var Picture
	 */
	public $picture;

	/**
	 * Indicates whether the user account has been verified.
	 *
	 * This is distinct from the is_verified field.
	 * Someone is considered verified if they take any of the following actions:
	 * <ul>
	 * <li>Register for mobile</li>
	 * <li>Confirm their account via SMS</li>
	 * <li>Enter a valid credit card</li>
	 * </ul>
	 *
	 * @var boolean
	 */
	public $verified;

	/**
	 * The person's relationship status.
	 *
	 * @var string
	 */
	public $relationship_status;
}
