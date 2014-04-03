<?php
/**
 * A CBDbEventedCommand factory behavior.
 *
 * You may attach this behaviour to a CBDbConnection to intercept command creation.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBDbEventedCommandFactory extends CBehavior
{
	/**
	 * Actually execute commands?
	 *
	 * @var boolean
	 */
	public $doActuallyExecute = false;

	/**
	 * Create a command instance.
	 *
	 * @param CDbConnection $dbConnection
	 * @param type $query
	 * @return CBDbEventedCommand
	 */
	public function createCommand(CDbConnection $dbConnection, $query=null)
	{
		$dbConnection->setActive(true);

		$dbCommand = new CBDbEventedCommand($dbConnection,$query);
		$dbCommand->onBeforeExecute = array($this, 'onExecuteCommand');
		$dbCommand->doActuallyExecute = $this->doActuallyExecute;

		return $dbCommand;
	}

	/**
	 * A command would be executed.
	 *
	 * @param CEvent $event
	 */
	public function onExecuteCommand(CEvent $event)
	{
		$this->raiseEvent('onExecuteCommand', $event);
	}
}