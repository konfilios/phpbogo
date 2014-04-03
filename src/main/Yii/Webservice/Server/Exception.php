<?php
/*
 */

namespace Bogo\Yii\Webservice\Server;

/**
 * An HTTP Exception accepting extra parameters.
 *
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Exception extends \CHttpException
{
	/**
	 * An array of parameters to be returned to the caller.
	 *
	 * @var array
	 */
	public $params;

	/**
	 * Create and throw an HTTP Exception with extra parameters.
	 *
	 * @param integer $statusCode
	 * @param string $statusMessage
	 * @param array $params
	 * @throws static
	 */
	static public function raise($statusCode, $statusMessage, $params = array())
	{
		$e = new static($statusCode, $statusMessage);
		$e->params = $params;
		throw $e;
	}
}
