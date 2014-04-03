bogo-yii-monitoring
===================

Real time monitoring &amp; notifications.

## Configuration

## Usage

In your base controller component, add and configure the following application component:

```php
// application components
'components'=>array(
	// [..]

	// Set proper subject prefix
	'developerNotifier' => array(
		'class' => 'CBDeveloperNotifier',
		'subjectPrefix' => 'MyExampleApp',
		'emailFrom' => 'monitoring@myexampleapp.com',
		'emailTo' => array(
			'developers@myexampleapp.com',
			'testers@myexampleapp.com',
			'operations@myexampleapp.com',
		),
	),

	// [..]
)
```


```php
class Controller extends CController
{
	/**
	 * Install PHP error and uncaught exception handlers.
	 */
	public function init()
	{
		// Call parent init
		parent::init();

		// Install uncaught PHP error handler
		Yii::app()->attachEventHandler('onError', array($this, 'onError'));
		// Install uncaught exception handler
		Yii::app()->attachEventHandler('onException', array($this, 'onException'));

		// [..]
	}


	/**
	 * Handle uncaught exception.
	 *
	 * @param CExceptionEvent $event
	 */
	public function onException($event)
	{
		$e = $event->exception;

		// Directly return an exception
		if (Yii::app()->developerNotifier) {
			Yii::app()->developerNotifier->sendErrorEmail($e->getCode(), $e->getMessage(), $e->getTraceAsString());
		}

		// Don't bubble up
//		$event->handled = true;
	}

	/**
	 * Handle uncaught PHP notice/warning/error.
	 *
	 * @param CErrorEvent $event
	 */
	public function onError($event)
	{
		//
		// Extract backtrace
		//
		$trace=debug_backtrace();
		// skip the first 4 stacks as they do not tell the error position
		if(count($trace)>4)
			$trace=array_slice($trace,4);

		$traceString = "#0 ".$event->file."(".$event->line."): ";
		foreach($trace as $i=>$t)
		{
			if ($i !== 0) {
				if(!isset($t['file']))
					$trace[$i]['file']='unknown';

				if(!isset($t['line']))
					$trace[$i]['line']=0;

				if(!isset($t['function']))
					$trace[$i]['function']='unknown';

				$traceString.="\n#$i {$trace[$i]['file']}({$trace[$i]['line']}): ";
			}
			if(isset($t['object']) && is_object($t['object']))
				$traceString.=get_class($t['object']).'->';
			$traceString.="{$trace[$i]['function']}()";

			unset($trace[$i]['object']);
		}

		if (Yii::app()->developerNotifier) {
			Yii::app()->developerNotifier->sendErrorEmail($event->code, $event->message, $traceString);
		}

		// Don't bubble up
//		$event->handled = true;
	}
}

```