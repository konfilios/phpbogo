<?php
/**
 * Nested transactions functionality.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
trait TBPdoWithNestedTransactions
{

	private $_transactionIsolationLevel = null;

	/**
	 * Transaction is still referenced but has been rolled back.
	 *
	 * @var boolean
	 */
	private $_isTransactionRolledBack = false;

	/**
	 * Nesting level of current transaction.
	 * @var integer
	 */
	private $_transactionRefcount = 0;

	/**
	 * Return transaction ref count.
	 *
	 * @return integer
	 */
	public function getTransactionRefCount()
	{
		return $this->_transactionRefcount;
	}

	/**
	 * Set transaction isolation level.
	 *
	 * @param string $isolationLevel
	 */
	public function setTransactionIsolationLevel($isolationLevel)
	{
		if ($this->_transactionIsolationLevel === null) {
			$this->_transactionIsolationLevel = $isolationLevel;
			$this->exec('SET TRANSACTION ISOLATION LEVEL '.$isolationLevel);

		} else if ($this->_transactionIsolationLevel != $isolationLevel) {
			throw new CDbException("Transaction isolation level can't be changed while a transaction is in progress");
		}
	}

	/**
	 * Begin new or resume existing transaction.
	 */
	public function beginTransaction()
	{
		if ($this->_transactionRefcount === 0) {
			// No transaction active so far
			parent::beginTransaction();
		}

		// Increase refcount
		$this->_transactionRefcount++;
	}

	/**
	 * Commit or decrease refcount of transaction.
	 */
	public function commit()
	{
		// One less is referencing this transaction
		$this->_transactionRefcount--;

		if (($this->_transactionRefcount === 0) && !$this->_isTransactionRolledBack) {
			// Nobody is referencing the transaction and it has not failed.
			// It's time we commit
			parent::commit();
			$this->_transactionIsolationLevel = null;
		}
	}

	/**
	 * Rollback or decrease refcount of transaction.
	 */
	public function rollback()
	{
		// One less is referencing this transaction
		$this->_transactionRefcount--;

		if (!$this->_isTransactionRolledBack) {
			// We have not rolled back yet. Rollback now instead of waiting for
			// refcount to reach 0 to avoid "leaks".
			// Try-and-catch is suggested in http://support.microsoft.com/kb/309335
			try {
				parent::rollBack();
				$this->_transactionIsolationLevel = null;
			} catch (Exception $e) {
				// Gargara
			}
		}

		// Mark as rolled back for as long as somebody is referencing this transaction
		$this->_isTransactionRolledBack = ($this->_transactionRefcount > 0);
	}
}