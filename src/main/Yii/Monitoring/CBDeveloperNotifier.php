<?php
/**
 * Developer notifier application component.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBDeveloperNotifier extends CApplicationComponent
{
	/**
	 * Sender email address.
	 *
	 * @var string
	 */
	public $emailFrom;

	/**
	 * Developer email addresses.
	 *
	 * @var string[]
	 */
	public $emailTo;

	/**
	 * Subject prefix.
	 *
	 * @var string
	 */
	public $subjectPrefix = '';

	/**
	 * Application component initialization.
	 */
	public function init()
	{
	}

	/**
	 * Create developer email and set addresses.
	 *
	 * @return CBDeveloperEmail
	 */
	public function createEmail()
	{
		$email = new CBDeveloperEmail();
		$email->from = $this->emailFrom;
		$email->to = $this->emailTo;
		return $email;
	}

	/**
	 * Send a success email notification.
	 */
	public function sendSuccessEmail()
	{
		$email = $this->createEmail();

		// Subject
		$email->subject = $this->getStandardSubjectString('Success');

		// Standard sections
		$this->setStandardDeveloperEmailSections($email);

		// Send
		$email->send();
	}

	/**
	 * Send an error email notification.
	 *
	 * @param integer $code
	 * @param string $message
	 * @param string $traceString
	 * @param string $utoken
	 */
	public function sendErrorEmail($code, $message, $traceString, $utoken = null)
	{
		$email = $this->createEmail();

		// Subject
		$email->subject = $this->getStandardSubjectString('Error', $utoken);

		// Error identity
		$email->pushSection('Error', array(
			'Message' => $message,
			'Code' => $code,
			'Stamp' => gmdate('Y-m-d H:i:s').' UTC',
			'Execution' => number_format(1000.0 * Yii::getLogger()->executionTime, 1).' ms'
		), CBDeveloperEmail::$colorError);

		// Error trace
		$email->pushSection('Trace', array(
			'' => $traceString
		));

		// Standard sections
		$this->setStandardDeveloperEmailSections($email);

		// Send
		$email->send();
	}

	/**
	 * Get current controller/action as string.
	 *
	 * @return string
	 */
	private function getStandardSubjectString($emailType, $utoken = null)
	{
		$controller = Yii::app()->controller;

		if (!empty($controller)) {
			$action = $controller->id.($controller->action ? '/'.$controller->action->id : '');
		}

		return (empty($this->subjectPrefix) ? '' : $this->subjectPrefix.' ')
			.$emailType
			.(empty($action) ? '' : ' @'.$action)
			.(empty($utoken) ? '' : ' #'.$utoken)
			.(empty($_SERVER) || empty($_SERVER['REMOTE_ADDR']) ? '' : ' <'.$_SERVER['REMOTE_ADDR']);
	}

	/**
	 * Set standard developer email sections for both success and error messages.
	 *
	 * @param CBDeveloperEmail $email
	 */
	private function setStandardDeveloperEmailSections($email)
	{
		// Request info
		$requestData = array();

		// Request payload
		$rawInput = file_get_contents('php://input');
		if (!empty($rawInput)) {
			$requestData['_PAYLOAD'] = CBJson::softDecode($rawInput);
		}

		// Get params
		if (!empty($_GET)) {
			$requestData['_GET'] = CBJson::softDecodeArray($_GET);
		}

		// Post params
		if (!empty($_POST)) {
			$requestData['_POST'] = CBJson::softDecodeArray($_POST);
		}

		// Headers
		$headers = Yii::app()->request->getRequestHeaders();
		if (!empty($_GET)) {
			$requestData['_HEADER'] = $headers;
		}

		// Cookies
		if (!empty($_COOKIE)) {
			$requestData['_COOKIE'] = $_COOKIE;
		}

		// Session
		if (!empty($_SESSION)) {
			$requestData['_SESSION'] = $_SESSION;
		}

		// Environment variables
		if (!empty($_SERVER)) {
			// Only get a meaningful subset of all environment variables
			$serverFields = array(
				'SERVER_ADDR',
				'SERVER_NAME',
//				'SERVER_PROTOCOL',
				'REQUEST_METHOD',
				'REMOTE_ADDR',
				'HTTP_REFERER'
			);

			$serverValues = array();
			foreach ($serverFields as $serverField) {
				if (!empty($_SERVER[$serverField])) {
					$serverValues[$serverField] = $_SERVER[$serverField];
				}
			}
			$requestData['_SERVER'] = $serverValues;
		}

		$email->pushSection('Request', $requestData);
	}

}