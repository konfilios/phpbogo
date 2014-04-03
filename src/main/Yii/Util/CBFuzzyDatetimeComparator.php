<?php
/**
 * Fuzzy datetime comparator attached to CDbCriteria components.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBFuzzyDatetimeComparator extends CBehavior
{
	/**
	 * Fuzzy datetime comparison.
	 *
	 * If parts of the datetime are missing, then a range query is created, with given parts
	 * kept constant and missing parts filled with extreme, valid values.
	 * 
	 * @param string $column
	 * @param string $value
	 * @param boolean $booleanOperator
	 * @return CDbCriteria
	 */
	public function compareFuzzyDatetime($column, $value, $toTimezoneAlias = null, $fromTimezoneAlias = null, $booleanOperator = 'AND')
	{
		// We belong to some cdbcriteria instance
		$criteria = $this->owner;
		/* @var $criteria CDbCriteria */

		// Support compare() operators. Value needs to be separated from operator first.
		// Sadly this needs to regexp re-evaluation when $criteria->compare() is called at the end.
		$matches = array();
		if (preg_match('/^(?:\s*(<>|<=|>=|<|>|=))?(.*)$/', $value, $matches)) {
			$value = $matches[2];
			$op = $matches[1];
		} else {
			$op = '';
		}

	    if (empty($value)) {
	        return $criteria;
		}

		// Keep original timezone
		$origTimezone = date_default_timezone_get();

		// Set new timezone
		if (!empty($fromTimezoneAlias)) {
			date_default_timezone_set($fromTimezoneAlias);
		}

		// Parse "operator-free" value into datetime parts.
		$partValues = date_parse($value);

		if ($partValues === false) {
			// Malformed request, silently ignore
			return $criteria;
		}

		// Make sure we have a year
		if (!isset($partValues['year'])) {
			// Current year
			$partValues['year'] = date('Y');
		}

		// Determine best monthday range
		if (isset($partValues['day'])) {
			// Given monthday
			$monthDayFrom = $monthDayTo = $partValues['day'];
		} else if (isset($partValues['month'])) {
			// Limits for given month
			$monthDayFrom = 1;
			$monthDayTo = cal_days_in_month(CAL_GREGORIAN, $partValues['month'], $partValues['year']);
		} else {
			// Arbitrary limits, better than nothing. 31 is convenient for december, too.
			$monthDayFrom = 1;
			$monthDayTo = 31;
		}

		$partRanges = array(
			'year' => array('%04d', $partValues['year'], $partValues['year'], array(false)),
			'month' => array('-%d', 1, 12, array(false)),
			'day' => array('-%d', $monthDayFrom, $monthDayTo, array(false)),
			'hour' => array(' %d', 0, 23, array(false)),
			'minute' => array(':%02d', 0, 59, array(false)),
			'second' => array(':%02d', 0, 59, array(false, 0)),
		);

		$inRangeMode = false;
		$finalDateFrom = '';
		$finalDateTo = '';
		foreach ($partRanges as $partName => $partInfo) {
			// Extra part information
			list($partFormat, $partMinValue, $partMaxValue, $partSkipValues) = $partInfo;

			if (!$inRangeMode) {
				// Extract real value
				$partValue = $partValues[$partName];

				$doSkip = false;
				foreach ($partSkipValues as $partSkipValue) {
					if ($partValue === $partSkipValue) {
						$doSkip = true;
						break;
					}
				}

				if ($doSkip) {
					// Part value not found, switch to range mode and let the next block handle this
					$inRangeMode = true;
					// From/to dates must be equal up to now
					$finalDateTo = $finalDateFrom;
				} else {
					// Part value found, still equality mode: Append value in proper format
					$finalDateFrom .= sprintf($partFormat, $partValue);
				}
			}

			if ($inRangeMode) {
				// Part value found, still equality mode: Append value in proper format
				$finalDateFrom .= sprintf($partFormat, $partMinValue);
				// Part value found, still equality mode: Append value in proper format
				$finalDateTo .= sprintf($partFormat, $partMaxValue);
			}
		}

		if (!empty($toTimezoneAlias)) {
			// Convert between timezones
			$finalStampFrom = strtotime($finalDateFrom);
			$finalStampTo = $finalDateTo ? strtotime($finalDateTo) : time();

			date_default_timezone_set($toTimezoneAlias);

			$finalDateFrom = date('Y-m-d H:i:s', $finalStampFrom);
			$finalDateTo = date('Y-m-d H:i:s', $finalStampTo);
		}

		if (!$inRangeMode) {
			// Not a range of dates, fallback to default behaviour
			$criteria->compare($column, $op.$finalDateFrom, false, $booleanOperator);
		} else {
			if ($op == '<>') {
				// Make it a NOT BETWEEN condition
				$criteria->addBetweenCondition("$column NOT", $finalDateFrom, $finalDateTo, $booleanOperator);
			} else {
				$criteria->addBetweenCondition($column, $finalDateFrom, $finalDateTo, $booleanOperator);
			}
		}

		// Restore system timezone
		date_default_timezone_set($origTimezone);

		return $criteria;
	}
}