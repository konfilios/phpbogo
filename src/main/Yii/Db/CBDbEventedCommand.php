<?php
/**
 * A CDbCommand that raises an event before actually executing.
 *
 * Such a command object may be useful when you want to intercept the commands that would
 * be executed and store them in a file as a script.
 *
 * It's obvious that if a command depends on the results of a previously, not executed one,
 * then it will fail. So use with caution.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBDbEventedCommand extends CDbCommand
{
	/**
	 * Actually execute commands?
	 *
	 * @var boolean
	 */
	public $doActuallyExecute = false;

	/**
	 * A command is to be executed.
	 *
	 * @param CEvent $event
	 */
	public function onBeforeExecute(CEvent $event)
	{
		$this->raiseEvent('onBeforeExecute', $event);
	}

	/**
	 * Raise event instead of executing.
	 *
	 * @param type $params
	 * @return integer number of rows affected by the execution.
	 */
	public function execute($params = array())
	{
		$this->onBeforeExecute(new CEvent($this, $params));

		if ($this->doActuallyExecute) {
			return parent::execute($params);
		} else {
			return 0;
		}
	}
}