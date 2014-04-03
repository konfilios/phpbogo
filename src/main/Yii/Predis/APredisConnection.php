<?php
/**
 * Represents a predis connection.
 *
 * This component relies on {@link https://github.com/nrk/predis Predis} library which is
 * particularly useful on a Windows setup where {@link https://github.com/nicolasff/phpredis phpredis}
 * build/installation might be either difficult or impossible.
 *
 * Here's a sample configuration of the component:
 *
 * <pre>
 * 		'redis'=>array(
 * 			'class'=>'ext.YiiRedis.APredisConnection',
 * 			'predisLibPath'=>'application.vendors.predis-0-8-3.lib.Predis',
 * 			'connectionParameters'=>array(
 * 				'database'=>0,
 * 				'host'=>'localhost',
 * 				'port'=>6379,
 * 			),
 * 		),
 * </pre>
 *
 *
 * @author Konstantinos Filios <konfilios@gmail.com>
 * @package Components
 */
class APredisConnection extends CApplicationComponent
{
	/**
	 * Path alias of the predis client library.
	 *
	 * @var string
	 */
	public $predisLibPath = "ext.predis.Predis";

	/**
	 * Connection parameters for one or multiple servers.
	 *
	 * Corresponds to the first parameter of the
	 * {@link https://github.com/nrk/predis/blob/v0.8/lib/Predis/Client.php Predis\Client}
	 * constructor.
	 *
	 * @var mixed
	 */
	public $connectionParameters = array(
		'host' => '127.0.0.1',
		'port' => 6379
	);

	/**
	 * Client options.
	 *
	 * Corresponds to the second parameter of the
	 * {@link https://github.com/nrk/predis/blob/v0.8/lib/Predis/Client.php Predis\Client}
	 * constructor.
	 *
	 * @var mixed
	 */
	public $clientOptions = null;

	/**
	 * User-defined commands.
	 *
	 * This is a hash of the form [$commandName => $commandClass] as expected in defineCommand()
	 * {@link https://github.com/nrk/predis/blob/v0.8/lib/Predis/Profile/ServerProfile.php}
	 *
	 *
	 * @var string[]
	 */
	public $defineCommands = array();

	/**
	 * Enable execution profiling.
	 *
	 * @var boolean
	 */
	public $enableProfiling = false;

	/**
	 * Predis client instance.
	 *
	 * @var Predis\Client
	 */
	private $_client = null;

	/**
	 * Application component initialization.
	 */
	public function init()
	{
		parent::init();

		if (!class_exists('Predis\\Client', false)) {
			// Fix autoloader
			require_once(Yii::getPathOfAlias($this->predisLibPath).'/Autoloader.php');
			Predis\Autoloader::register();
		}
	}

	/**
	 * The predis client instance.
	 *
	 * @return Predis\Client 
	 */
	public function getClient()
	{
		if ($this->_client === null) {
			if($this->enableProfiling) {
				$profileToken = 'bogo.yii.predis.APredisConnection.create()';
				Yii::beginProfile($profileToken, 'bogo.yii.predis.APredisConnection');
			} else {
				$profileToken = null;
			}

			// Create instance
			$this->_client = new Predis\Client($this->connectionParameters, $this->clientOptions);

			// Define commands
			$profile = $this->_client->getProfile();
			/* @var $profile Predis\Profile\ServerProfile */

			// Make sure defineCommands is an array
			if (empty($this->defineCommands)) {
				$this->defineCommands = array();
			}

			if (!isset($this->defineCommands['delete'])) {
				$this->defineCommands['delete'] = 'Predis\Command\KeyDelete';
			}

			foreach ($this->defineCommands as $alias=>$command) {
				$profile->defineCommand($alias, $command);
			}

			if ($profileToken) {
				Yii::endProfile($profileToken, 'bogo.yii.predis.APredisConnection');
			}
		}

		return $this->_client;
	}

	/**
	 * Forward call to client instance.
	 */
	public function __call($method, $args)
	{
		if($this->enableProfiling) {
			$profileToken = 'bogo.yii.predis.APredisConnection.'.$method.'()';
		} else {
			$profileToken = null;
		}

		if ($profileToken) {
			Yii::beginProfile($profileToken, 'bogo.yii.predis.APredisConnection');
		}

		$result = call_user_func_array(array($this->getClient(), $method), $args);

		if ($profileToken) {
			Yii::endProfile($profileToken, 'bogo.yii.predis.APredisConnection');
		}

		return $result;
	}

}
