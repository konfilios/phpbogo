<?php
/**
 * Extend base CDbConnection functionality.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBDbConnection extends CDbConnection
{
	/**
	 * @var string Custom PDO wrapper class.
	 * @since 1.1.8
	 */
	public $pdoClass = 'CBPdo';

	/**
	 * Mapping between PDO driver and schema class name.
	 *
	 * A schema class can be specified using path alias.
	 * Mysql and Mssql schemata are overriden here to support
	 * extended functionality
	 *
	 * @var array
	 */
	public $driverMap=array(
		'pgsql'=>'CPgsqlSchema',    // PostgreSQL
		'mysqli'=>'CBMysqlSchema',   // MySQL
		'mysql'=>'CBMysqlSchema',    // MySQL
		'sqlite'=>'CSqliteSchema',  // sqlite 3
		'sqlite2'=>'CSqliteSchema', // sqlite 2
		'mssql'=>'CBMssqlSchema',    // Mssql driver on windows hosts
		'dblib'=>'CBMssqlSchema',    // dblib drivers on linux (and maybe others os) hosts
		'sqlsrv'=>'CBMssqlSchema',   // Mssql
		'oci'=>'COciSchema',        // Oracle driver
	);

	/**
	 * Set transaction isolation level.
	 *
	 * @param string $isolationLevel
	 */
	public function setTransactionIsolationLevel($isolationLevel)
	{
		$this->getPdoInstance()->setTransactionIsolationLevel($isolationLevel);
	}

	/**
	 * Creates the PDO instance.
	 * When some functionalities are missing in the pdo driver, we may use
	 * an adapter class to provides them.
	 * @return PDO the pdo instance
	 */
	protected function createPdoInstance()
	{
		$pdoClass=$this->pdoClass;
		if(($pos=strpos($this->connectionString,':'))!==false)
		{
			$driver=strtolower(substr($this->connectionString,0,$pos));
			if($driver==='mssql' || $driver==='dblib' || $driver==='sqlsrv')
				$pdoClass='CBPdoMssql';
		}
		return new $pdoClass($this->connectionString,$this->username,
									$this->password,$this->attributes);
	}

	/**
	 * Allow custom command factories.
	 *
	 * @param mixed $query
	 * @return CDbCommand
	 */
	public function createCommand($query = null)
	{
		$commandFactory = $this->asa('commandFactory');

		if ($commandFactory) {
			return $commandFactory->createCommand($this, $query);
		} else {
			return parent::createCommand($query);
		}
	}
}
