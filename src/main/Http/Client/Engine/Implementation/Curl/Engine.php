<?php
/* 
 */

namespace Bogo\Http\Client\Engine\Implementation\Curl;
use Bogo\Http\Client;

class Engine extends Client\Engine\Base
{
	public function createSingleCallRunner(Client\Call $call)
	{
		return new SingleRunner($this, $call);
	}

	public function createMultiCallRunner()
	{
		return new MultiRunner();
	}
}
