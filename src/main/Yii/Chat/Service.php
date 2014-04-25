<?php
/*
 */
namespace Bogo\Yii\Chat;

/**
 * Chat service managing chat threads.
 *
 * @author Konstantinos Filios <konfilios@gmail.com>
 * @since 2.0
 */
class Service extends \CApplicationComponent
{
	
	public $messageModelClass = 'ChatMessage';
	public $nodeModelClass = 'ChatNode';

	/**
	 * Create a new message and corresponding thread nodes.
	 *
	 * @param integer $senderUserId Sender user id.
	 * @param integer $recipientUserId Recipient user id.
	 * @param string $body Message body.
	 * @return boolean
	 */
	public function createMessage($senderUserId, $recipientUserId, $body)
	{
		// Row lock contention probability for all the following queries
		// is low since they are of interest for only two users (sender and recipient)
		$messageModelClass = $this->messageModelClass;
		$nodeModelClass = $this->nodeModelClass;
		$trn = $messageModelClass::model()->dbConnection->beginTransaction();

		// Prepare nodes
		$newNodesData = array(
			$senderUserId => array(
				'slaveUserId' => $recipientUserId,
				'isIncoming' => 0,
				'isNew' => 0
			),
			$recipientUserId => array(
				'slaveUserId' => $senderUserId,
				'isIncoming' => 1,
				'isNew' => 1
			)
		);

		// Fix insert/update order to avoid database deadlocks
		ksort($newNodesData);

		try {
			// Mark previously terminal nodes as non-terminal in O(2) update queries
			foreach ($newNodesData as $masterUserId=>$nodeData) {
				$nodeModelClass::model()->updateAll(array(
					'isTerminal' => 0
				), 'masterUserId = :masterUserId AND slaveUserId = :slaveUserId AND isTerminal = 1', array(
					':masterUserId' => $masterUserId,
					':slaveUserId' => $nodeData['slaveUserId']
				));
			}

			// Create new message in O(1) insert queries
			$newMessage = new $messageModelClass();
			$newMessage->senderUserId = $senderUserId;
			$newMessage->recipientUserId = $recipientUserId;
			$newMessage->body = $body;
			$newMessage->createdUdatetime = $newMessage->stampToUdatetime();

			$newMessage->saveOrThrow();

			// Create new nodes in O(2) insert queries
			foreach ($newNodesData as $masterUserId=>$nodeData) {

				// Create sender node
				$node = new $nodeModelClass();
				$node->masterUserId = $masterUserId;
				$node->slaveUserId = $nodeData['slaveUserId'];
				$node->messageId = $newMessage->id;
				$node->isIncoming = $nodeData['isIncoming'];
				$node->isNew = $nodeData['isNew'];
				$node->isTerminal = 1;
				$node->saveOrThrow();
			}

			$trn->commit();

		} catch (Exception $e) {
			$trn->rollback();
			throw $e;
		}

		return true;
	}

	/**
	 * Mark all threads of messages for which $slaveUserId is the recipient as not new (aka read).
	 *
	 * @param integer $slaveUserId Slave user who has received the messages.
	 * @return boolean
	 */
	public function markAllMessagesSentFromUserAsNotNew($masterUserId, $slaveUserId)
	{
		$nodeModelClass = $this->nodeModelClass;
		return $nodeModelClass::model()->updateAll(array(
			'isNew' => 0
		), ':masterUserId = :masterUserId AND slaveUserId = :slaveUserId AND isIncoming = 1 AND isNew = 1', array(
			':masterUserId' => $masterUserId,
			':slaveUserId' => $slaveUserId
		));
	}

	/**
	 * Delete a whole thread between given master and slave user.
	 *
	 * @param int target user id
	 * @param int user id
	 * @return boolean
	 */
	public function removeThreadWithUser($masterUserId, $slaveUserId)
	{
		$nodeModelClass = $this->nodeModelClass;
		return $nodeModelClass::model()->deleteAllByAttributes(array(
			'masterUserId' => $masterUserId,
			'slaveUserId' => $slaveUserId
		));
	}

	/**
	 * Delete a message (node) from thread.
	 *
	 * @param integer $deletedNodeId Node id of message to delete.
	 * @return boolean
	 */
	public function removeMessageByNodeId($deletedNodeId)
	{
		$nodeModelClass = $this->nodeModelClass;
		$dbConnection = $nodeModelClass::model()->dbConnection;

		$dbConnection->setTransactionIsolationLevel('SERIALIZABLE');
		$trn = $dbConnection->beginTransaction();

		try {
			// Load node to delete
			$deletedNode = $nodeModelClass::model()->findByPk($deletedNodeId); /* @var $deletedNode Node */

			if (empty($deletedNode)) {
				throw new CException('Node with id '.$deletedNodeId.' not found');
			}

			// Delete the node
			$deletedNode->delete();

			if ($deletedNode->isTerminal) {
				// Node was terminal. We have to replace it with another terminal
				$newTerminalNode = $nodeModelClass::model()
					->scopeOrderBy('nodeId DESC')
					->findByAttributes(array(
						'masterUserId' => $deletedNode->masterUserId,
						'slaveUserId' => $deletedNode->slaveUserId,
						'isTerminal' => 0
					));

				if ($newTerminalNode) {
					// A new terminal node was elected
					$newTerminalNode->isTerminal = 1;
					$newTerminalNode->saveOrThrow();
				}
			}

			$trn->commit();

		} catch (Exception $e) {
			$trn->rollback();
			throw $e;
		}

		return true;
	}

	/**
	 * Count received unread messages.
	 *
	 * @param integer $masterUserId User id to which the unread messages belong.
	 * @return integer
	 */
	public function getReceivedUnreadMessageCount($masterUserId)
	{
		$nodeModelClass = $this->nodeModelClass;
		return $nodeModelClass::model()
			->countByAttributes(array(
				'masterUserId' => $masterUserId,
				'isNew' => 1
			));
	}

	/**
	 * All messages $masterUserId has exchanged with $slaveUserId.
	 *
	 * @param integer $masterUserId Master user.
	 * @param integer $slaveUserId Slave user.
	 * @return Node[]
	 */
	public function findAllMessagesWithUser($masterUserId, $slaveUserId)
	{
		$nodeModelClass = $this->nodeModelClass;
		return $nodeModelClass::model()
			->with('message')
			->scopeOrderBy('t.id DESC')
			->findAllByAttributes(array(
				'masterUserId' => $masterUserId,
				'slaveUserId' => $slaveUserId,
			));
	}

	/**
	 * Latest message sent from $sendUserId to $masterUserId.
	 *
	 * @param integer Recipient user id.
	 * @param integer Sender user id.
	 * @return Node
	 */
	public function findLatestMessageReceivedByUser($recipientUserId, $senderUserId)
	{
		$nodeModelClass = $this->nodeModelClass;
		return $nodeModelClass::model()
			->with('message')
			->scopeOrderBy('id DESC')
			->findByAttributes(array(
				'masterUserId' => $recipientUserId,
				'slaveUserId' => $senderUserId,
				'isIncoming' => 1
			));
	}

	/**
	 * Find all terminal messages $masterUserId is involved in.
	 *
	 * @param integer $masterUserId
	 * @return Node[]
	 */
	public function findAllLatestTerminalMessages($masterUserId)
	{
		$nodeModelClass = $this->nodeModelClass;
		return $nodeModelClass::model()
			->with('message')
			->scopeOrderBy('t.id DESC')
			->findAllByAttributes(array(
				'masterUserId' => $masterUserId,
				'isTerminal' => 1
			));
	}
}
