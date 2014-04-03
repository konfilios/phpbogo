<?php
namespace Bogo\Util;

/**
 * Datetime/calendar functions.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Calendar
{
	/**
	 * Number of seconds in an hour
	 */
	const SECONDS_IN_AN_HOUR = 3600;
	/**
	 * Number of seconds in a day
	 */
	const SECONDS_IN_A_DAY = 86400;

	/**
	 * Create an array of hours ranging from $from to $to (inclusive),
	 * optionally appending $appendString to each hour's label
	 *
	 * @param integer $from
	 * @param integer $to
	 * @param string $appendString
	 * @return array
	 */
	static public function hourRange($from, $to, $appendString = ':00')
	{
		$hours = array();
		for ($i = $from; $i <= $to; $i++) {
			$hours[$i] = sprintf("%02d", $i).$appendString;
		}
		return $hours;
	}

	/**
	 * Formats $fromDate as if it was expressed in $fromTimezoneAlias into a new date with format
	 * $format converting it to $toTimezoneAlias. Result is formatted using date()
	 *
	 * @param mixed $fromDate Source date, either as timestamp or date parsable by strtotime()
	 * @param string $format
	 * @param string $toTimezoneAlias
	 * @param string $fromTimezoneAlias
	 * @return string
	 */
	static public function dateToTimezone($fromDate, $format, $toTimezoneAlias, $fromTimezoneAlias = 'UTC')
	{
		// Keep original timezone
		$origTimezoneAlias = date_default_timezone_get();

		if (is_int($fromDate) || is_numeric($fromDate)) {
			// It's a UTC timestamp already
			$stamp = intval($fromDate);
		} else {
			// Convert date to timestamp using $fromTimezoneAlias
			date_default_timezone_set($fromTimezoneAlias);
			$stamp = intval(strtotime($fromDate));
		}

		// Convert stamp to date using
		date_default_timezone_set($toTimezoneAlias);
		$toDate = date($format, $stamp);

		// Revert to original timezone
		date_default_timezone_set($origTimezoneAlias);

		return $toDate;
	}

	/**
	 * Format a human-readable representation (sec-min-hr-d-wk) of elapsed time.
	 *
	 * @param integer $elapsedTimeInSeconds Elapsed time in seconds.
	 * @param boolean $short If true, zero components are omitted
	 * @return string
	 */
	static public function formatElapsedTime($elapsedTimeInSeconds, $short = false)
	{
		// Retrieve seconds
		$formatSeconds = $elapsedTimeInSeconds % 60;
		$elapsedTimeInMinutes = ($elapsedTimeInSeconds - $formatSeconds) / 60;
		if (!$short || !empty($formatSeconds)) {
			$format = sprintf('%02dsec', $formatSeconds);
		} else {
			$format = '';
		}

		if (empty($elapsedTimeInMinutes)) {
			return $format;
		}

		// Retrieve minutes
		$formatMinutes = $elapsedTimeInMinutes % 60;
		$elapsedTimeInHours = ($elapsedTimeInMinutes - $formatMinutes) / 60;
		if (!$short || !empty($formatMinutes)) {
			$format = sprintf('%02dmin ', $formatMinutes).$format;
		}

		if (empty($elapsedTimeInHours)) {
			return $format;
		}

		// Retrieve hours
		$formatHours = $elapsedTimeInHours % 24;
		$elapsedTimeInDays = ($elapsedTimeInHours - $formatHours) / 24;
		if (!$short || !empty($formatHours)) {
			$format = sprintf('%02dhr ', $formatHours).$format;
		}

		if (empty($elapsedTimeInDays)) {
			return $format;
		}

		// Retrieve days
		$formatDays = $elapsedTimeInDays % 7;
		$elapsedTimeInWeeks = ($elapsedTimeInDays - $formatDays) / 7;
		if (!$short || !empty($formatDays)) {
			$format = sprintf('%dd ', $formatDays).$format;
		}

		if (empty($elapsedTimeInWeeks)) {
			return $format;
		}

		// Retrieve weeks
		return sprintf('%dwk ', $elapsedTimeInWeeks).$format;
	}

	/**
	 * Extends strtotime() php function with optional timezone.
	 *
	 * @param type $datetimeString
	 * @param type $useTimezoneAlias
	 * @return type
	 */
	static public function stringToTimestamp($datetimeString, $useTimezoneAlias = null)
	{
		if ($useTimezoneAlias === null) {
			$timestamp = strtotime($datetimeString);
		} else {
			// Keep original timezone
			$origTimezoneAlias = date_default_timezone_get();

			date_default_timezone_set($useTimezoneAlias);

			$timestamp = strtotime($datetimeString);

			// Revert to original timezone
			date_default_timezone_set($origTimezoneAlias);
		}

		return $timestamp;
	}
}
