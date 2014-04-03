<?php
/**
 * A dummy implementation of a queued http request consumer session repository.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBDummyQueuedHttpRequestConsumerSessionRepository implements IBQueuedHttpRequestConsumerSessionRepository
{
	/**
	 * Creates a new session.
	 *
	 * @param integer $expirationStamp Timestamp of session expiration.
	 * @return integer Id of created session.
	 */
	public function createSession($expirationStamp)
	{
		return 1;
	}

	/**
	 * Refreshes an existing session expiration.
	 *
	 * @param integer $sessionId Session id to refresh.
	 * @param integer $expirationStamp Timestamp of session expiration.
	 */
	public function refreshSession($sessionId, $expirationStamp)
	{
	}

	/**
	 * Deletes a session.
	 *
	 * @param integer $sessionId
	 */
	public function deleteSession($sessionId)
	{
	}

	/**
	 * Deletes expired sessions.
	 *
	 * @return integer[] Array of deleted session ids.
	 */
	public function deleteExpiredSessions()
	{
	}
}
