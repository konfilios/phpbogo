<?php
/**
 * Extend CSMyqlSchema functionality.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBMysqlSchema extends CMysqlSchema
{
	/**
	 * @var array the abstract column types mapped to physical column types.
	 * @since 1.1.6
	 */
	public $columnTypes=array(
		'pk' => 'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
		'string' => 'varchar(255)',
		'text' => 'text',
		'integer' => 'int(11)',
		'float' => 'float',
		'decimal' => 'decimal',
		'datetime' => 'datetime',
		'timestamp' => 'timestamp',
		'time' => 'time',
		'date' => 'date',
		'binary' => 'blob',
		'boolean' => 'tinyint(1)',
		'money' => 'decimal(19,4)',

		// Extra datatypes
		'uuid' => 'char(36)',
		'uuidfk' => 'char(36)',

		'datestamp' => 'datetime NOT NULL',

		'mediumstring' => 'varchar(1022)',
		'bigstring' => 'varchar(4000)',
		'maxstring' => 'varchar(65535)',

		// Extra primary key datatypes
		'pk*' => 'int(11) NOT NULL PRIMARY KEY',
		'stringpk' => 'varchar(255) NOT NULL PRIMARY KEY',

		'intpk' => 'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
		'intpk*' => 'int(11) NOT NULL PRIMARY KEY',

		'tinyintpk' => 'tinyint NOT NULL AUTO_INCREMENT PRIMARY KEY',
		'tinyintpk*' => 'tinyint NOT NULL PRIMARY KEY',

		'smallintpk' => 'smallint NOT NULL AUTO_INCREMENT PRIMARY KEY',
		'smallintpk*' => 'smallint NOT NULL PRIMARY KEY',

		'bigintpk' => 'bigint NOT NULL AUTO_INCREMENT PRIMARY KEY',
		'bigintpk*' => 'bigint NOT NULL PRIMARY KEY',

		'uuidpk' => 'char(36) PRIMARY KEY NOT NULL',
		'uuidpk*' => 'char(36) PRIMARY KEY NOT NULL',
	);

	/**
	 * SQL snippet that converts expression to given type.
	 *
	 * @see http://dev.mysql.com/doc/refman/5.0/en/cast-functions.html#function_convert
	 *
	 * @param string $expr
	 * @param string $toType
	 * @return string
	 */
	public function convertExpressionType($expr, $toType)
	{
		return $expr;
	}
}
