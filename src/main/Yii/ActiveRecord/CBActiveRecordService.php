<?php
/**
 * ActiveRecord service.
 *
 * Supports a queue of events to be raised after the transaction of
 * the model's database commits successfully.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBActiveRecordService extends CApplicationComponent
{
	/**
	 * Parent master model class.
	 *
	 * @var string
	 */
	public $masterModelClass;

	/**
	 * Initialize.
	 * 
	 * Attach handlers for transaction-related events.
	 */
	public function init()
	{
		parent::init();

		// Auto-raise events when transaction is commited.
		$masterModelClass = $this->masterModelClass;
		$masterModelClass::model()->dbConnection->attachEventHandler('onClearLastTransaction',
				array($this, 'handleClearLastTransaction'));
	}

	/**
	 * Handles CBDbConnection.onClearLastTransaction event.
	 *
	 * @param CEvent $event
	 */
	public function handleClearLastTransaction(CEvent $event)
	{
		if ($event->params['commited']) {
			// Last transaction was commited
			$this->flushQueuedCommitEvents();
		} else {
			// Last transaction was rolled back
			$this->clearQueuedCommitEvents();
		}
	}

	/**
	 * List of post-commit queued events.
	 *
	 * @var array
	 */
	private $queuedCommitEvents = array();

	/**
	 * Queue a post-commit event.
	 *
	 * @param type $name
	 * @param CEvent $event
	 */
	public function queueCommitEvent($name, CEvent $event) {
		$this->queuedCommitEvents[] = array($name, $event);
	}

	/**
	 * Raise all post-commit queued events and clear queue.
	 */
	public function flushQueuedCommitEvents() {
		foreach ($this->queuedCommitEvents as $queuedEvent) {
			// Extract queued event info
			list($name, $event) = $queuedEvent;

			// Fire the event
			$this->$name($event);
		}

		$this->clearQueuedCommitEvents();
	}

	/**
	 * Clear post-commit event queue.
	 */
	public function clearQueuedCommitEvents()
	{
		$this->queuedCommitEvents = array();
	}
}
