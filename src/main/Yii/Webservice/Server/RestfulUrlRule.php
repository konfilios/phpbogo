<?php
/* 
 */

namespace Bogo\Yii\Webservice\Server;

class RestfulUrlRule extends \CUrlRule
{
	/**
	 * True if controller represents a restful resource.
	 *
	 * @var boolean
	 */
	public $isResource = false;

	/**
	 * Available verbs
	 * @var string
	 */
	public $verb = 'GET,POST,PUT,PATCH,DELETE,HEAD,OPTIONS';

	/**
	 * Construct rule.
	 *
	 * After standard construction, we initialize the non-standard $isResource variable.
	 *
	 * @param string|array $route
	 * @param string $pattern
	 */
	public function __construct($route, $pattern)
	{
		parent::__construct($route, $pattern);

		if (is_array($route) && !empty($route['isResource'])) {
			$this->isResource = true;
		}
	}

	/**
	 * Parses a URL based on this rule.
	 *
	 * After the default parsing is run, the verb is appended as the action if this is
	 * an $isResource = 1 rule.
	 *
	 * @param CUrlManager $manager the URL manager
	 * @param CHttpRequest $request the request object
	 * @param string $pathInfo path info part of the URL
	 * @param string $rawPathInfo path info that contains the potential URL suffix
	 * @return mixed the route that consists of the controller ID and action ID or false on error
	 */
	public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
	{
		$route = parent::parseUrl($manager, $request, $pathInfo, $rawPathInfo);

		if (!empty($route) && $this->isResource) {
			$routeComponents = explode('/', $route);

			$routeComponents[1] = strtolower($request->getRequestType());

			$route = implode('/', $routeComponents);
		}
		return $route;
	}
}