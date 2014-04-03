<?php
/* 
 */

namespace Bogo\Facebook\Connector;
use Bogo\Util\Inet;

class Util
{
	/**
	 * IPv4 address ranges of FB Cralwer/Scraper
	 *
	 * Facebook Crawler IP ranges:
	 * https://developers.facebook.com/docs/ApplicationSecurity/#facebook_scraper
	 * @var string[]
	 */
	static private $fbScraperIpv4Ranges = array(
		'31.13.24.0/21',
		'31.13.64.0/18',
		'66.220.144.0/20',
		'69.63.176.0/20',
		'69.171.224.0/19',
		'74.119.76.0/22',
		'103.4.96.0/22',
		'173.252.64.0/18',
		'204.15.20.0/22',
	);

	/**
	 * IPv6 address ranges of FB Cralwer/Scraper
	 *
	 * Facebook Crawler IP ranges:
	 * https://developers.facebook.com/docs/ApplicationSecurity/#facebook_scraper
	 * @var string[]
	 */
	static private $fbScraperIpv6Ranges = array(
		'2401:db00::/32',
		'2620:0:1c00::/40',
		'2a03:2880::/32',
	);


	/**
	 * Test if an IP belongs to the facebook crawler/scraper.
	 *
	 * Facebook Crawler IP ranges:
	 * https://developers.facebook.com/docs/ApplicationSecurity/#facebook_scraper
	 *
	 * @param string $testIp
	 * @return boolean
	 */
	static function isFbScraperIp($testIp)
	{
		// Test IPv4 CIDR ranges first
		foreach (self::$fbScraperIpv4Ranges as $ipv4Range) {
			if (Inet::matchesCidrIp4($testIp, $ipv4Range)) {
				// Match, it's facebook
				return true;
			}
		}

		// Test IPv6 CIDR ranges last
		foreach (self::$fbScraperIpv6Ranges as $ipv6Range) {
			if (Inet::matchesCidrIp4($testIp, $ipv6Range)) {
				// Match, it's facebook
				return true;
			}
		}

		// No range matched, it's not fb
		return false;
	}
}
