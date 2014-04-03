<?php
/**
 * Extend CSMssqlSchema functionality.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBMssqlSchema extends CMssqlSchema
{
	/**
	 * @var array the abstract column types mapped to physical column types.
	 * @since 1.1.6
	 */
    public $columnTypes=array(
        'pk' => 'int IDENTITY PRIMARY KEY',
        'string' => 'nvarchar(255)',
        'text' => 'ntext',
        'integer' => 'int',
        'float' => 'float',
        'decimal' => 'decimal',
        'datetime' => 'datetime',
        'timestamp' => 'timestamp',
        'time' => 'time',
        'date' => 'date',
        'binary' => 'binary',
        'boolean' => 'bit',
		'money' => 'decimal(19,4)',

		// Extra datatypes
		'uuid' => 'uniqueidentifier NOT NULL DEFAULT (newid())',
		'uuidfk' => 'uniqueidentifier',

		'datestamp' => 'datetime NOT NULL DEFAULT (getutcdate())',

		'mediumstring' => 'nvarchar(1022)',
		'bigstring' => 'nvarchar(4000)',
		'maxstring' => 'nvarchar(max)',

		// Extra primary key datatypes
		'stringpk' => 'nvarchar(255) PRIMARY KEY',
		'pk*' => 'int PRIMARY KEY',

        'intpk' => 'int IDENTITY PRIMARY KEY',
		'intpk*' => 'int PRIMARY KEY',

		'tinyintpk' => 'tinyint IDENTITY PRIMARY KEY',
		'tinyintpk*' => 'tinyint PRIMARY KEY',

		'smallintpk' => 'smallint IDENTITY PRIMARY KEY',
		'smallintpk*' => 'smallint PRIMARY KEY',

		'bigintpk' => 'bigint IDENTITY PRIMARY KEY',
		'bigintpk*' => 'bigint PRIMARY KEY',

		'uuidpk' => 'uniqueidentifier PRIMARY KEY NOT NULL DEFAULT (newid())',
		'uuidpk*' => 'uniqueidentifier PRIMARY KEY NOT NULL',
    );

	/**
	 * SQL snippet that converts expression to given type.
	 *
	 * @see http://msdn.microsoft.com/en-us/library/ms187928.aspx
	 *
	 * @param string $expr
	 * @param string $toType
	 * @return string
	 */
	public function convertExpressionType($expr, $toType)
	{
		return 'CONVERT('.$toType.', '.$expr.')';
	}
}