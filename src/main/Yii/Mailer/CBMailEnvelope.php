<?php
/**
 * A mail envelope.
 * 
 * Contains from/to addresses. An envelope is required by CBMailer::send() method.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBMailEnvelope
{
	/**
	 * Optional sender address.
	 * @var string|string[]
	 */
	public $from;
	/**
	 * Optional reply-to address.
	 * @var string|string[]
	 */
	public $replyTo;
	/**
	 * Array of recipient addresses.
	 * @var array
	 */
	public $to;
	/**
	 * Array of Carbon Copy recipient addresses.
	 * @var array
	 */
	public $cc;
	/**
	 * Array of Blind Carbon Copy (aka undisclosed) recipient address(es).
	 * @var type
	 */
	public $bcc;

	/**
	 * Init-constructor.
	 *
	 * @param array attributes
	 */
	public function __construct(array $attributes = array())
	{
		foreach ($attributes as $attribute=>$value) {
			$this->$attribute = $value;
		}
	}
}