<?php
/*
 * https://developers.facebook.com/docs/graph-api/reference/page/
 */

namespace Bogo\Facebook\Model;

class Page extends NamedObject
{
	public function attributeTypes()
	{
		return parent::attributeTypes() + array(
			'category_list' => 'Bogo\Facebook\Model\PageCategory[]',
			'location' => 'Bogo\Facebook\Model\Location',
			'parking' => 'Bogo\Facebook\Model\PlaceParking',
			'picture' => 'Bogo\Facebook\Model\Picture',
		);
	}

	/**
	 * The Page's category.
	 *
	 * e.g. Product/Service, Computers/Technology
	 *
	 * @var string
	 */
	public $category;

	/**
	 * The Page's categories.
	 *
	 * @var PageCategory[]
	 */
	public $category_list;

	/**
	 * The location of this place. Applicable to all Places.
	 *
	 * @var Location
	 */
	public $location;

	/**
	 * Number of checkins at a place represented by a Page.
	 *
	 * @var integer
	 */
	public $checkins;

	/**
	 * The number of users who like the Page.
	 *
	 * For Global Brand Pages this is the count for all pages across the brand.
	 *
	 * @var integer
	 */
	public $likes;

	/**
	 * The Page's Facebook URL.
	 *
	 * @var string
	 */
	public $link;

	/**
	 * Indicates the opening hours for this location.
	 *
	 * Sample:
     hours:
      { mon_1_open: '11:00',
        mon_1_close: '01:30',
        tue_1_open: '11:00',
        tue_1_close: '01:30',
        wed_1_open: '11:00',
        wed_1_close: '01:30',
        thu_1_open: '11:00',
        thu_1_close: '01:30',
        fri_1_open: '11:00',
        fri_1_close: '01:30',
        sat_1_open: '11:00',
        sat_1_close: '01:30',
        sun_1_open: '11:00',
        sun_1_close: '01:30' },
	 *
	 * @var string[]
	 */
	public $hours;

	/**
	 * Indicates whether the Page is published and visible to non-admins.
	 *
	 * @var boolean
	 */
	public $is_published;

	/**
	 * Informaton about the parking available at a place.
	 *
	 * @var PlaceParking
	 */
	public $parking;

	/**
	 * Phone number provided by a Page.
	 *
	 * @var string
	 */
	public $phone;

	/**
	 * The number of people talking about this Page.
	 *
	 * @var integer
	 */
	public $talking_about_count;

	/**
	 * The URL of the Page's website.
	 *
	 * @var string
	 */
	public $website;

	/**
	 * The number of visits to this Page's location.
	 *
	 * @var integer
	 */
	public $were_here_count;

	/**
	 * Information about the Page's cover photo.
	 *
	 * @var CoverPhoto
	 */
	public $cover;

	/**
	 * The person's profile picture.
	 *
	 * @var Picture
	 */
	public $picture;
}
