<?php
/**
 * PDO implementing application-level nested transactions.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBPdo extends PDO
{
	use TBPdoWithNestedTransactions;
}