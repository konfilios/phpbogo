<?php
/* 
 */

namespace Bogo\Facebook\Model;

class Location extends Object
{
	/**
	 * Address or street.
	 *
	 * @var string
	 */
	public $street;

	/**
	 * City name.
	 *
	 * @var string
	 */
	public $city;

	/**
	 * State name.
	 *
	 * @var string
	 */
	public $state;

	/**
	 * Country name.
	 *
	 * @var string
	 */
	public $country;

	/**
	 * Zip code.
	 *
	 * @var string
	 */
	public $zip;

	/**
	 * Latitude.
	 *
	 * @var double
	 */
	public $latitude;

	/**
	 * Longitude.
	 *
	 * @var double
	 */
	public $longitude;
}