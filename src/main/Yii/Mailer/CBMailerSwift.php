<?php
/**
 * Concrete mailer wrapping the Swift Mailer.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBMailerSwift extends CBMailer
{
	/**
	 * Transport class.
	 *
	 * Valid options are:
	 * <ul>
	 * <li><b>smtp</b>: SMTP protocol. Requires <i>smtpHost</i> parameter.
	 * You may optionally change the <i>smtpPort</i> parameter (defaults to 25).
	 * You may optionally set <i>smtpUsername</i>, <i>smtpPassword</i> and
	 * <i>smtpEncryption</i> settings (default to empty).
	 * </li>
	 * <li><b>sendmail</b>: Sendmail agent. You may optionally change the <i>sendmailCommand</i> parameter.</li>
	 * <li><b>mail</b>: Native php mail() functionality. No parameters.</li>
	 * </ul>
	 *
	 * @var string
	 */
	public $transport = 'sendmail';
	/**
	 * SMTP outgoing mail server host.
	 *
	 * @var string
	 */
	public $smtpHost;

	/**
	 * Outgoing SMTP server port.
	 *
	 * @var string
	 */
	public $smtpPort = 25;

	/**
	 * SMTP Password.
	 *
	 * @var string
	 */
	public $smtpUsername;

	/**
	 * SMTP email.
	 *
	 * @var string
	 */
	public $smtpPassword;

	/**
	 * SMTP encryption for security.
	 *
	 * @var string
	 */
	public $smtpEncryption;

	/**
	 * Sendmail command.
	 *
	 * @var string
	 */
	public $sendmailCommand = '/usr/bin/sendmail -t';

	/**
	 * CApplicationComponent initialization.
	 */
	public function init()
	{
		parent::init();

		// Fix Swift autoloader
		spl_autoload_unregister(array('YiiBase', 'autoload'));
		require_once(dirname(__FILE__).'/../vendors/Swift-5.0.0/lib/swift_required.php');
		spl_autoload_register(array('YiiBase', 'autoload'));
	}

	/**
	 * Send email message.
	 *
	 * @param CBMailEnvelope|array $envelope
	 * @param CBMailMessage|array $message
	 * @return boolean True on success
	 */
	public function send($envelope, $message)
	{
		if (is_array($message)) {
			$message = new CBMailMessage($message);
		}

		if (is_array($envelope)) {
			$envelope = new CBMailEnvelope($envelope);
		}

		//Create the Transport
		$swiftTransport = $this->loadTransport();

		//Create the Mailer using your created Transport
		$swiftMailer = Swift_Mailer::newInstance($swiftTransport);

		//Create a message
		$fromAddressString = CBMailer::makeNamedAddressAssoc($this->getProperFrom($envelope));
		$swiftMessage = Swift_Message::newInstance($message->subject)
				->setFrom($fromAddressString)
				->setTo($envelope->to);

		$swiftMessage->addPart($message->body, $message->contentType);

		if ($this->inDebugMode) {
			Yii::trace(print_r(array(
				'from' => $fromAddressString,
				'to' => $envelope->to,
				'subject' => $message->subject,
				'body' => $message->body,
				'contentType' => $message->contentType
			), true), 'bogo-yii-mailer.CBMailerNativePhpMail');

			return true;
		} else {
			$result = $swiftMailer->send($swiftMessage);

			if ($this->doLogActivity == true) {
				if (!$result) {
					$logMessage = 'Failed to send "'.$message->subject.'" email to ['.$this->makeNamedAddressesString($envelope->to).']'
							."\nMessage:\n".$message->body;
					Yii::log($logMessage, 'error', 'bogo-yii-mailer.CBMailerSwift');
				} else {
					$logMessage = 'Sent email "'.$message->subject.'" to ['.$this->makeNamedAddressesString($envelope->to).']'
							."\nMessage:\n".$message->body;
					Yii::log($logMessage, 'trace', 'bogo-yii-mailer.CBMailerSwift');
				}
			}
			return $result;

		}
	}

	/**
	 * Loads appropriate transport according to config.
	 *
	 * @return Swift_Transport
	 */
	protected function loadTransport()
	{
		switch ($this->transport) {
		case 'smtp':
			$transport = Swift_SmtpTransport::newInstance($this->smtpHost, $this->smtpPort, $this->smtpEncryption);

			if ($this->smtpUsername) {
				$transport->setUsername($this->smtpUsername);
			}

			if ($this->smtpPassword) {
				$transport->setPassword($this->smtpPassword);
			}

			break;

		case 'mail':
			$transport = Swift_MailTransport::newInstance();
			break;

		case 'sendmail':
			$transport = Swift_SendmailTransport::newInstance($this->sendmailCommand);
			break;

		default:
			throw new CException(500, 'Invalid transport "'.$this->transport.'" for swift mailer.');
		}

		return $transport;
	}
}
