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
		$trn = Message::model()->dbConnection->beginTransaction();

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
				Node::model()->updateAll(array(
					'isTerminal' => 0
				), 'masterUserId = :masterUserId AND slaveUserId = :slaveUserId AND isTerminal = 1', array(
					':masterUserId' => $masterUserId,
					':slaveUserId' => $nodeData['slaveUserId']
				));
			}

			// Create new message in O(1) insert queries
			$new_message = new Message();
			$new_message->senderUserId = $senderUserId;
			$new_message->recipientUserId = $recipientUserId;
			$new_message->body = $body;
			$new_message->createdUdatetime = $new_message->stampToUdatetime();

			$new_message->saveOrThrow();

			// Create new nodes in O(2) insert queries
			foreach ($newNodesData as $masterUserId=>$nodeData) {

				// Create sender node
				$node = new Node();
				$node->masterUserId = $masterUserId;
				$node->slaveUserId = $nodeData['slaveUserId'];
				$node->messageId = $new_message->messageId;
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
	public function markAllMessagesSentToUserAsNotNew($slaveUserId)
	{
		return Node::model()->updateAll(array(
			'isNew' => 0
		), 'slaveUserId = :slaveUserId AND isIncoming = 1 AND isNew = 1', array(
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
		return Node::model()->deleteAllByAttributes(array(
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
		$dbConnection = Node::model()->dbConnection;

		$dbConnection->setTransactionIsolationLevel('SERIALIZABLE');
		$trn = $dbConnection->beginTransaction();

		try {
			// Load node to delete
			$deletedNode = Node::model()->findByPk($deletedNodeId); /* @var $deletedNode Node */

			if (empty($deletedNode)) {
				throw new CException('Node with id '.$deletedNodeId.' not found');
			}

			// Delete the node
			$deletedNode->delete();

			if ($deletedNode->isTerminal) {
				// Node was terminal. We have to replace it with another terminal
				$newTerminalNode = Node::model()
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
		return Node::model()
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
		return Node::model()
			->with('message')
			->scopeOrderBy('id DESC')
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
		return Node::model()
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
		return Node::model()
			->scopeOrderBy('id DESC')
			->findAllByAttributes(array(
				'masterUserId' => $masterUserId,
				'isTerminal' => 1
			));
	}
}
