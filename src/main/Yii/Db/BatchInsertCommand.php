<?php
/*
 */
namespace Bogo\Yii\Db;

class BatchInsertCommand extends \CDbCommand
{
	public function batchInsert($tableName, $columnNames, $rowValues)
	{
		$params = array();

		// Calculate column names
		$columnNamesSql = '';
		foreach ($columnNames as $columnName) {
			$columnNamesSql .= ($columnNamesSql ? ', ' : '').$this->connection->quoteColumnName($columnName);
		}

		$allValuesSql = '';
		foreach ($rowValues as $i => $row) {

			$placeholders = array();
			foreach ($row as $field => $value) {
				if ($value instanceof CDbExpression) {
					$placeholders[] = $value->expression;

					foreach ($value->params as $paramName => $paramValue) {
						$params[$paramName] = $paramValue;
					}
				} else {
					$paramName = ':'.$field.$i;

					$placeholders[] = $paramName;
					$params[$paramName] = $value;
				}
			}
			$allValuesSql .= ($allValuesSql ? ', ' : '').'('.implode(', ', $placeholders).')';
		}
		$sql = 'INSERT INTO '.$this->connection->quoteTableName($tableName)
				.' ('.$columnNamesSql.') VALUES '.$allValuesSql;

		return $this->setText($sql)->execute($params);
	}

}