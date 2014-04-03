<?php
/**
 * Abstract mailer application component.
 *
 * Defines a standard interface for concrete mailers.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
abstract class CBMailer extends CApplicationComponent
{
	/**
	 * Optional transport headers.
	 * @var string[]
	 */
	public $transportHeaders = array();

	/**
	 * From address (and name).
	 * @var string|string[]
	 */
	public $from;

	/**
	 * Reply-to address (and name).
	 * @var string|string[]
	 */
	public $replyTo;

	/**
	 * Enforces set fromAddress, otherwise it's used as fallback.
	 *
	 * @var boolean
	 */
	public $doEnforceFromAddress = false;

	/**
	 * Enforces set replyToAddress, otherwise it's used as fallback.
	 *
	 * @var boolean
	 */
	public $doEnforceReplyToAddress = false;

	/**
	 * Allows to turn actual dispatch off.
	 *
	 * Useful for debugging purposes.
	 *
	 * @var boolean
	 */
	public $inDebugMode = true;

	/**
	 * Turns on activity logging for debugging purposes.
	 *
	 * @var boolean
	 */
	public $doLogActivity = false;

	/**
	 * CApplicationComponent initialization.
	 */
	public function init()
	{
		parent::init();
	}

	/**
	 * Create a named address assoc.
	 *
	 * Recognized formats:
	 * <ul>
	 * <li>(Array)array('address'=>'name')</li>
	 * <li>(String)$address</li>
	 * </ul>
	 *
	 * @param string|string[] $rawAddress
	 * @return array
	 */
	static public function makeNamedAddressAssoc($rawAddress)
	{
		return is_array($rawAddress) ? $rawAddress : array($rawAddress=>null);
	}

	/**
	 * Pick 'from' address either from envelope or mailer.
	 *
	 * @param CBMailEnvelope $envelope
	 * @return string
	 * @throws CException
	 */
	public function getProperFrom($envelope)
	{
		if ($this->doEnforceFromAddress) {
			// Enforce
			return $this->from;
		} else {
			// Fallback
			return $envelope->from ?: $this->from;
		}
	}

	/**
	 * Pick 'from' address either from envelope or mailer.
	 *
	 * @param CBMailEnvelope $envelope
	 * @return string
	 * @throws CException
	 */
	public function getProperReplyTo($envelope)
	{
		if ($this->doEnforceReplyToAddress) {
			// Enforce
			return $this->replyTo;
		} else {
			// Fallback
			return $envelope->replyTo ?: $this->replyTo;
		}
	}
	/**
	 * Create a named address string from a named address assoc.
	 *
	 * @param string[] $namedAddressAssoc
	 * @return string
	 */
	protected function makeNamedAddressString($namedAddressAssoc)
	{
		list($address, $name) = each($namedAddressAssoc);

		return $name ? $name.' <'.$address.'>' : $address;
	}

	/**
	 * Create a csv of name addresses.
	 *
	 * @param type $rawAddresses
	 * @return string
	 */
	protected function makeNamedAddressesString($rawAddresses)
	{
		$str = '';
		foreach ($rawAddresses as $rawAddress) {
			$str .= ($str ? ', ' : '').$this->makeNamedAddressString(self::makeNamedAddressAssoc($rawAddress));
		}
		return $str;
	}

	/**
	 * Send email message.
	 *
	 * @param CBMailEnvelope|array $envelope
	 * @param CBMailMessage|array $message
	 * @return boolean True on success
	 */
	abstract public function send($envelope, $message);
}