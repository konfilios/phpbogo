<?php
/*
 */

namespace Bogo\Yii\Webservice\Server;
use Bogo\Types;

/**
 * Represents a JSON action that is defined as a controller method.
 *
 * This subclass enforces the at-most-one-parameter-with-type-hinting actions.
 *
 * @since 1.0
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class InlineAction extends \CInlineAction
{
	/**
	 * Executes a method of an object with the supplied named parameters.
	 *
	 * This method is internally used and implements all Restful logic.
	 *
	 * @param mixed $object the object whose method is to be executed
	 * @param ReflectionMethod $method the method reflection
	 * @param array $request the named parameters
	 * @return mixed whether the named parameters are valid
	 */
	protected function runWithParamsInternal($object, $method, $request)
	{
		$methodParams = $method->getParameters();

		if (!$request instanceof HttpRequest) {
			throw new \CHttpException(500, 'HTTP Request object should be an instance of Bogo\Yii\Webservice\Service\HttpRequest');
		}
		/* @var $request HttpRequest */

		// Extract invocation params
		$invokeParams = array();
		foreach ($methodParams as $methodParam) {
			/* @var $methodParam ReflectionParameter */
			$paramName = $methodParam->name;

			// Only allow certain parameter names
			switch ($paramName) {
			case 'query':
				$paramValue = $request->getQueryAssoc();
				break;
			case 'body':
				$paramValue = $request->getBodyAssoc();
				break;
			case 'header':
				$paramValue = $request->getHeaderAssoc();
				break;
			default:
				throw new \CHttpException(500, 'Unexpected controller action parameter "'.$paramName.'"');
			}

			if ($paramValue !== null) {
				// A value exists, let's parse it
				$methodParamClass = $methodParam->getClass();
				/* @var $methodParamClass ReflectionClass */

				if (!empty($methodParamClass)) {
					//
					// Expecting a value of given class
					//
					$methodParamClassName = $methodParamClass->name;

					if ($paramValue === null) {
						$paramObject = null;
					} else {
						$paramObject = Types\Engine::copyAttributesDeep($paramValue, new $methodParamClassName);
					}

					$invokeParams[] = $paramObject;

				} else if ($methodParam->isArray()) {
					//
					// Expecting array
					//
					$invokeParams[] = is_array($paramValue) ? $paramValue : array($paramValue);

				} else {
					//
					// Pass param through
					//
					$invokeParams[] = $paramValue;
				}
			} else if ($methodParam->isDefaultValueAvailable()) {
				//
				// No value found, but param is optional, so use default
				//
				$invokeParams[] = $methodParam->getDefaultValue();
			} else {
				//
				// No value found and param is mandatory, die
				//
				throw new \CHttpException(400, $method->class.'::'.$method->name.': '
					.'No argument passed for mandatory parameter "'.$paramName.'"');
			}
		}

		// Invoke
		return $method->invokeArgs($object, $invokeParams);
	}
}
