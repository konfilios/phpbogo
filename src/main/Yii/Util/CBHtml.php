<?php
/**
 * Utility extensions to CHtml.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBHtml extends CHtml
{
	/**
	 * Yes/no options.
	 *
	 * @var string[]
	 */
	static public $yesNo = array('1'=>'Yes', '0'=>'No');

	/**
	 * True/false options.
	 *
	 * @var string[]
	 */
	static public $trueFalse = array('1'=>'True', '0'=>'False');

	/**
	 * On/off options.
	 *
	 * @var string[]
	 */
	static public $onOff = array('1'=>'On', '0'=>'Off');

	/**
	 * Live/draft options.
	 *
	 * @var string[]
	 */
	static public $liveDraft = array('1'=>'Live', '0'=>'Draft');

	/**
	 * Return percentage format of $a/$b
	 *
	 * @param int $nominator
	 * @param int $denominator
	 * @param int $precisionDigits
	 * @return string
	 */
	static public function percentage($nominator, $denominator, $precisionDigits = 1)
	{
		if (!empty($denominator)) {
			return Yii::app()->format->number((100.0 * $nominator) / $denominator, $precisionDigits)."%";
		} else {
			return 'N/A';
		}
	}

	/**
	 * Return string Ratio $a/$b or N/A
	 *
	 * @param int $nominator
	 * @param int $denonimator
	 * @param int $precisionDigits
	 * @return string
	 */
	static public function ratio($nominator, $denonimator, $precisionDigits = 1)
	{
		if (!empty($denonimator)) {
			return number_format($nominator / $denonimator, $precisionDigits);
		} else {
			return 'N/A';
		}
	}

	/**
	 * Range in [$from, $to] inclusive.
	 *
	 * @param integer $from
	 * @param integer $to
	 * @param integer $step
	 * @return integer[]
	 */
	static public function range($from, $to, $step = 1)
	{
		$range = array();
		for ($i = $from; $i <= $to; $i++) {
			$range[$i] = $i;
		}
		return $range;
	}

	/**
	 * English ordinal suffix of given number.
	 * @param integer $numericValue
	 * @return string
	 *
	 * @see http://www.if-not-true-then-false.com/2010/php-1st-2nd-3rd-4th-5th-6th-php-add-ordinal-number-suffix/
	 */
	static public function englishOrdinalSuffix($numericValue)
	{
		if (!in_array(($numericValue % 100), array(11, 12, 13))) {
			switch ($numericValue % 10) {
			// Handle 1st, 2nd, 3rd
			case 1:
				return 'st';
			case 2:
				return 'nd';
			case 3:
				return 'rd';
			}
		}
		return 'th';
	}

	/**
	 * Convenience function for english ordinal number formatting.
	 * @param integer $numericValue
	 * @param string $padding
	 * @return string
	 */
	static public function englishOrdinalNumber($numericValue, $padding = '')
	{
		return $numericValue.$padding.self::englishOrdinalSuffix($numericValue);
	}
}