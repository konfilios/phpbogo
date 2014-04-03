<?php
/**
 * A simple redis block guard.
 *
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class ARedisGuard extends ARedisEntity
{
	/**
	 * Block until a client calls notify().
	 *
	 * @param integer $blockTimeoutSeconds Block timeout. If zero, blocks for ever.
	 * @return mixed Null on timeout, some value pushed through notify() otherwise.
	 */
	public function wait($blockTimeoutSeconds = 0.0)
	{
		$redis = $this->getConnection()->getClient();

		$element = $redis->blpop($this->name, $blockTimeoutSeconds);

		return $element ? $element[1] : null;
	}

	/**
	 * Wake up a client blocked on wait()
	 *
	 * @param mixed $value Value to pass to wait()ers.
	 * @return integer
	 */
	public function notify($value = 1)
	{
		$redis = $this->getConnection()->getClient();
		$script =
<<<LUA
				if (redis.call('LLEN', KEYS[1]) == 0) then
					return redis.call('RPUSH', KEYS[1], ARGV[1]);
				else
					return 1;
				end
LUA;

		return $redis->eval($script, 1, $this->name, $value);
	}
}
