<?php
/**
 * Concrete mailer using native PHP mail() function.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBMailerNativePhpMail extends CBMailer
{

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

		//
		// Start with transport headers
		//
		$finalHeaders = $this->transportHeaders;

		//
		// Copy envelope info
		//
		if ($envelope->cc) {
			$finalHeaders['Cc'] = $this->makeNamedAddressesString($envelope->cc);
		}
		if ($envelope->bcc) {
			$finalHeaders['Bcc'] = $this->makeNamedAddressesString($envelope->bcc);
		}

		//
		// Merge message headers
		//
		$finalHeaders['Content-type'] = $message->contentType.'; '.$message->charset;

		//
		// Customly managed headers
		//
		$finalHeaders['From'] = $this->makeNamedAddressString(CBMailer::makeNamedAddressAssoc($this->getProperFrom($envelope)));
		$finalHeaders['Reply-to'] = $this->makeNamedAddressString(CBMailer::makeNamedAddressAssoc($this->getProperReplyTo($envelope)));
		$finalHeaders['MIME-Version'] = '1.0';

		//
		// Convert headers into a string
		//
		$finalHeadersString = '';
		foreach ($finalHeaders as $headerName=>$headerValue) {
			if (!empty($headerValue)) {
				$finalHeadersString .= ($finalHeadersString ? "\r\n" : '').$headerName.': '.$headerValue;
			}
		}

		//
		// Create recipients string
		//
		$toAddressesString = $this->makeNamedAddressesString($envelope->to);

		// Send email
		if ($this->inDebugMode) {
			Yii::trace(print_r(array(
				'to' => $toAddressesString,
				'subject' => $message->subject,
				'body' => $message->body,
				'headers' => $finalHeadersString
			), true), 'bogo-yii-mailer.CBMailerNativePhpMail');

			return true;
		} else {
			return mail($toAddressesString, $message->subject, $message->body, $finalHeadersString);
		}
	}
}