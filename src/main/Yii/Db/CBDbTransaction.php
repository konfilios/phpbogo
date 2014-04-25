<?php
/**
 * Bogo db transaction.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBDbTransaction extends CDbTransaction
{

	/**
	 * Commits a transaction.
	 * 
	 * Also raises the onCommitTransaction event.
	 *
	 * @throws CException if the transaction or the DB connection is not active.
	 */
	public function commit() {
		parent::commit();

		if ($this->connection->getPdoInstance()->getTransactionRefCount() == 0) {
			$this->connection->onCommitTransaction(new CEvent($this->connection));
		}
	}

	/**
	 * Rolls back a transaction.
	 * 
	 * Also raises the onRollbackTransaction event.
	 *
	 * @throws CException if the transaction or the DB connection is not active.
	 */
	public function rollback() {
		parent::rollback();

		if ($this->connection->getPdoInstance()->getTransactionRefCount() == 0) {
			$this->connection->onRollbackTransaction(new CEvent($this->connection));
		}
	}

	/**
	 * Run $func within the transaction.
	 *
	 * @param callable $func User code to be run within the transaction.
	 * @return mixed $func's return value or true.
	 * @throws Exception
	 */
	public function run($func)
	{
		try {
			$ret = $func();

			// Commit
			$this->commit();

			return $ret ?: true;

		} catch (Exception $e) {
			$this->rollback();
			throw $e;
		}
	}

	/**
	 * Tell the connection we're done.
	 */
	public function __destruct()
	{
		$this->connection->clearLastTransaction();
	}
}
