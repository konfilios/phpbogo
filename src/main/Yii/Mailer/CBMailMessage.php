<?php
/**
 * Mail message.
 *
 * A CBMailer can send CBMailMessage instances using CBMailer::send() method.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBMailMessage extends CComponent
{
	/**
	 * Message subject.
	 * @var string
	 */
	public $subject;

	/**
	 * Message body.
	 * @var string
	 */
	public $body;

	/**
	 * Content type.
	 * @var string
	 */
	public $contentType = 'text/html';

	/**
	 * Message character set.
	 * @var string
	 */
	public $charset = 'utf-8';

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
