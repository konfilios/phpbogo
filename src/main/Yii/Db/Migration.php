<?php
namespace Bogo\Yii\Db;

/**
 * Extensions to CDbMigration.
 *
 * For a mapping between MySQL and MSSQL datatypes see
 * http://technet.microsoft.com/en-us/library/cc966396.aspx
 * 
 * For creating yii migrations see
 * http://www.yiiframework.com/doc/guide/1.1/en/database.migration
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Migration extends \CDbMigration
{
	/**
	 * Refresh cached db schema.
	 *
	 * This should be done after any action that modifies the schema, including
	 * changing the db connection.
	 */
	public function refreshDbSchema()
	{
		$this->dbConnection->schema->refresh();
	}

	/**
	 * Sets the currently active database connection.
	 * The database connection will be used by the methods such as {@link insert}, {@link createTable}.
	 * @param \CDbConnection $db the database connection component
	 */
	public function setDbConnection($db)
	{
		parent::setDbConnection($db);
		$this->refreshDbSchema();
	}

	/**
	 * Builds and executes a SQL statement for adding a new DB column.
	 * @param string $tableName the table that the new column will be added to. The table name will be properly quoted by the method.
	 * @param string $columnName the name of the new column. The name will be properly quoted by the method.
	 * @param string $columnType the column type. The {@link getColumnType} method will be invoked to convert abstract column type (if any)
	 * into the physical one. Anything that is not recognized as abstract type will be kept in the generated SQL.
	 * For example, 'string' will be turned into 'varchar(255)', while 'string not null' will become 'varchar(255) not null'.
	 */
	public function addColumn($tableName, $columnName, $columnType)
	{
		parent::addColumn($this->dbConnection->tablePrefix.$tableName, $columnName, $columnType);
	}

	/**
	 * Builds and executes a SQL statement for dropping a DB column.
	 * @param string $tableName the table whose column is to be dropped. The name will be properly quoted by the method.
	 * @param string $columnName the name of the column to be dropped. The name will be properly quoted by the method.
	 */
	public function dropColumn($tableName, $columnName)
	{
		parent::dropColumn($this->dbConnection->tablePrefix.$tableName, $columnName);
	}

	/**
	 * Builds and executes a SQL statement for renaming a column.
	 * @param string $tableName the table whose column is to be renamed. The name will be properly quoted by the method.
	 * @param string $oldColumnName the old name of the column. The name will be properly quoted by the method.
	 * @param string $newColumnName the new name of the column. The name will be properly quoted by the method.
	 */
	public function renameColumn($tableName, $oldColumnName, $newColumnName)
	{
		parent::renameColumn($this->dbConnection->tablePrefix.$tableName, $oldColumnName, $newColumnName);
	}

	/**
	 * Builds and executes a SQL statement for changing the definition of a column.
	 * @param string $tableName the table whose column is to be changed. The table name will be properly quoted by the method.
	 * @param string $columnName the name of the column to be changed. The name will be properly quoted by the method.
	 * @param string $newColumnType the new column type. The {@link getColumnType} method will be invoked to convert abstract column type (if any)
	 * into the physical one. Anything that is not recognized as abstract type will be kept in the generated SQL.
	 * For example, 'string' will be turned into 'varchar(255)', while 'string not null' will become 'varchar(255) not null'.
	 */
	public function alterColumn($tableName, $columnName, $newColumnType)
	{
		parent::alterColumn($this->dbConnection->tablePrefix.$tableName, $columnName, $newColumnType);
	}

	/**
	 * Builds and executes a SQL statement for truncating a DB table.
	 * @param string $tableName the table to be truncated. The name will be properly quoted by the method.
	 */
	public function truncateTable($tableName)
	{
		return parent::truncateTable($this->dbConnection->tablePrefix.$tableName);
	}

	/**
	 * Builds and executes a SQL statement for renaming a DB table.
	 * @param string $tableName the table to be renamed. The name will be properly quoted by the method.
	 * @param string $newTableName the new table name. The name will be properly quoted by the method.
	 */
	public function renameTable($tableName, $newTableName)
	{
		return parent::renameTable($this->dbConnection->tablePrefix.$tableName,
				$this->dbConnection->tablePrefix.$newTableName);
	}

	/**
	 * Builds and executes a SQL statement for dropping a DB table.
	 * @param string $tableName the table to be dropped. The name will be properly quoted by the method.
	 */
	public function dropTable($tableName)
	{
		return parent::dropTable($this->dbConnection->tablePrefix.$tableName);
	}

	/**
	 * Returns all table names in the database.
	 * @param string $schema the schema of the tables. Defaults to empty string, meaning the current or default schema.
	 * If not empty, the returned table names will be prefixed with the schema name.
	 * @return array all table names in the database.
	 */
	public function getTableNames($schema='')
	{
		$this->refreshDbSchema();
		$allTableNames = $this->dbConnection->schema->getTableNames($schema);
		$tablePrefix = $this->dbConnection->tablePrefix;
		$tablePrefixLength = strlen($tablePrefix);

		if (!empty($tablePrefix)) {
			// Purge tables not matching prefix
			foreach ($allTableNames as $i=>$tableName) {
				if (substr($tableName, 0, $tablePrefixLength) != $tablePrefix) {
					unset($allTableNames[$i]);
				}
			}
		}

		return $allTableNames;
	}

	/**
	 * Returns the metadata for all tables in the database.
	 * @param string $schema the schema of the tables. Defaults to empty string, meaning the current or default schema.
	 * @return array the metadata for all tables in the database.
	 * Each array element is an instance of {@link CDbTableSchema} (or its child class).
	 * The array keys are table names.
	 */
	public function getTables($schema='')
	{
		$this->refreshDbSchema();
		$allTables = $this->dbConnection->schema->getTables($schema);
		$tablePrefix = $this->dbConnection->tablePrefix;
		$tablePrefixLength = strlen($tablePrefix);

		if (!empty($tablePrefix)) {
			// Purge tables not matching prefix
			foreach ($allTables as $tableName=>$table) {
				if (substr($tableName, 0, $tablePrefixLength) != $tablePrefix) {
					unset($allTables[$tableName]);
				}
			}
		}

		return $allTables;
	}

	/**
	 * Check if a table exists.
	 *
	 * @param string $searchTable
	 * @return boolean
	 */
	public function tableExists($searchTable)
	{
		$searchTable = $this->dbConnection->tablePrefix.$searchTable;
		return in_array($searchTable, $this->dbConnection->schema->getTableNames());
	}

	/**
	 * Builds and executes a SQL statement for creating a new DB table.
	 *
	 * The columns in the new  table should be specified as name-definition pairs (e.g. 'name'=>'string'),
	 * where name stands for a column name which will be properly quoted by the method, and definition
	 * stands for the column type which can contain an abstract DB type.
	 * The {@link getColumnType} method will be invoked to convert any abstract type into a physical one.
	 *
	 * If a column is specified with definition only (e.g. 'PRIMARY KEY (name, type)'), it will be directly
	 * inserted into the generated SQL.
	 *
	 * @param string $table the name of the table to be created. The name will be properly quoted by the method.
	 * @param array $columns the columns (name=>definition) in the new table.
	 * @param string $options additional SQL fragment that will be appended to the generated SQL.
	 */
	public function createTable($table, $columns, $options=null)
	{
		return parent::createTable($this->dbConnection->tablePrefix.$table, $columns, $options);
	}

	/**
	 * Consistent name of foreign key constraints.
	 *
	 * @param string $fromTableName
	 * @param string $fromColumnName
	 * @return string
	 */
	protected function getConsistentForeignKeyName($fromTableName, $fromColumnName)
	{
		return 'fk_'.strtolower($fromTableName.'_'.$fromColumnName);
	}

	/**
	 * Builds a SQL statement for adding a foreign key constraint to an existing table.
	 *
	 * The method will properly quote the table and column names and automatically
	 * pick a consistent name for the new constraint based on fromTable and
	 * fromColumns.
	 *
	 * @param string $fromTable the table that the foreign key constraint will be added to.
	 * @param string $fromColumns the name of the column to that the constraint will be added on. If there are multiple columns, separate them with commas.
	 * @param string $refTable the table that the foreign key references to.
	 * @param string $refColumns the name of the column that the foreign key references to. If there are multiple columns, separate them with commas.
	 * @param string $delete the ON DELETE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
	 * @param string $update the ON UPDATE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
	 */
	public function addConsistentForeignKey($fromTable, $fromColumns, $refTable, $refColumns, $delete=null, $update=null)
	{
		$this->addForeignKey($this->getConsistentForeignKeyName($fromTable, $fromColumns),
				$fromTable, $fromColumns, $refTable, $refColumns, $delete, $update);
	}

	/**
	 * Builds a SQL statement for dropping a foreign key constraint.
	 * @param string $tableNames the table whose foreign is to be dropped. The name will be properly quoted by the method.
	 * @param string $columnNames the name of the foreign key constraint to be dropped. The name will be properly quoted by the method.
	 */
	public function dropConsistentForeignKey($tableName, $columNames)
	{
		$this->dropForeignKey($this->getConsistentForeignKeyName($tableName, $columNames), $tableName);
	}

	/**
	 * Consistent name of indexes.
	 *
	 * @param string $fromTableName
	 * @param string $fromColumnName
	 * @return string
	 */
	protected function getConsistentIndexName($fromTableName, $fromColumnName)
	{
		return 'idx_'.strtolower(str_replace(',', '_', $fromColumnName));
	}


	/**
	 * Builds and executes a SQL statement for creating a new index.
	 *
	 * @param string $tableName the table that the new index will be created for. The table name will be properly quoted by the method.
	 * @param string $columnNames the column(s) that should be included in the index. If there are multiple columns, please separate them
	 * by commas. The column names will be properly quoted by the method.
	 * @param boolean $isUnique whether to add UNIQUE constraint on the created index.
	 */
	public function createConsistentIndex($tableName, $columnNames, $isUnique=false)
	{
		$tableName = $this->dbConnection->tablePrefix.$tableName;

		return $this->createIndex($this->getConsistentIndexName($tableName, $columnNames),
				$tableName, $columnNames, $isUnique);
	}

	/**
	 * Builds and executes a SQL statement for dropping an index.
	 *
	 * @param string $tableName the table whose index is to be dropped. The name will be properly quoted by the method.
	 * @param string $columnNames the name of the index to be dropped. The name will be properly quoted by the method.
	 */
	public function dropConsistentIndex($tableName, $columnNames)
	{
		$tableName = $this->dbConnection->tablePrefix.$tableName;
		$this->dropIndex($this->getConsistentIndexName($tableName, $columnNames), $tableName);
	}

	/**
	 * Quickly create an index and add foreign key.
	 *
	 * @param string $fromTableName
	 * @param string $fromColumnName
	 * @param string $toTableName
	 * @param string $toColumnName
	 * @param string $onDelete
	 * @param string $onUpdate
	 * @param boolean $isUnique
	 */
	public function addConsistentIndexAndForeignKey($fromTableName, $fromColumnName,
			$toTableName, $toColumnName,
			$onDelete = null, $onUpdate = null, $isUnique = false)
	{
		$this->createConsistentIndex($fromTableName, $fromColumnName, $isUnique);

		$fromTableName = $this->dbConnection->tablePrefix.$fromTableName;
		$toTableName = $this->dbConnection->tablePrefix.$toTableName;

		$this->addConsistentForeignKey($fromTableName, $fromColumnName,
				$toTableName, $toColumnName,
				$onDelete, $onUpdate);
	}

	/**
	 * Builds a SQL statement for dropping a foreign key constraint and an index.
	 * @param string $tableNames the table whose foreign is to be dropped. The name will be properly quoted by the method.
	 * @param string $columnNames the name of the foreign key constraint to be dropped. The name will be properly quoted by the method.
	 */
	public function dropConsistentIndexAndForeignKey($tableName, $columNames)
	{
		$this->dropConsistentForeignKey($this->dbConnection->tablePrefix.$tableName, $columNames);

		$this->dropConsistentIndex($tableName, $columNames);
	}

	/**
	 * Drop all application tables.
	 */
	public function truncateDatabase()
	{
//		$this->dbConnection->schema->checkIntegrity(false);

		$tables = $this->getTables();
		echo "    > truncate database ...\n";
		$time=microtime(true);

		// Drop foreign keys first
		foreach ($tables as $table) {

//			echo "    > drop ".$tableName." foreign keys ...\n";
			foreach ($table->foreignKeys as $foreinKeyFromCol=>$foreignKeyTo) {
				$this->dropConsistentForeignKey($table->name, $foreinKeyFromCol);
			}
		}

		// Drop tables
		foreach ($tables as $table) {
//			echo "    > drop ".$tableName." table ...\n";
			parent::dropTable($table->name);
		}

//		$this->dbConnection->schema->checkIntegrity(true);
		$this->refreshDbSchema();

		echo " done truncate db (time: ".sprintf('%.3f', microtime(true)-$time)."s)\n";
	}

	/**
	 * Update model using passed attribute values.
	 *
	 * @param CActiveRecord $model
	 * @param array $updateAttributes
	 * @return integer Number of records updated.
	 */
	public function updateAllModels($model, $updateAttributes)
	{
		$model->refreshMetaData();
		echo 'Updating all models of type '.get_class($model).'... ';
		$countAffected = $model->updateAll($updateAttributes);
		echo ' '.$countAffected." updated\n";
		return $countAffected;
	}

	/**
	 * Delete all model records.
	 * @param CActiveRecord $model
	 * @return integer Number of records deleted.
	 */
	public function deleteAllModels($model)
	{
		$model->refreshMetaData();
		echo 'Deleting all models of type '.get_class($model).'... ';
		$countAffected = $model->deleteAll();
		echo ' '.$countAffected." deleted\n";
		return $countAffected;
	}

	/**
	 * Check if a table column exists.
	 *
	 * @param string $tableName
	 * @param string $columnName
	 * @param boolean $refreshTableSchema
	 * @return boolean
	 */
	public function columnExists($tableName, $columnName, $refreshTableSchema = false)
	{
		return in_array($columnName, $this->dbConnection->schema->getTable($tableName, $refreshTableSchema)->columnNames);
	}
}