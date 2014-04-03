<?php
/**
 * Message exchange with CGridView.
 *
 * @link http://www.yiiframework.com/wiki/205/how-to-show-ajax-delete-status-in-cgridview-like-flash-messages/
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBGridViewMessenger
{
	static public $messageTypes = array('error', 'success', 'info');

	/**
	 * Flash or echo a message of given type.
	 *
	 * This function is used from within the controller actions.
	 * 
	 * @param string $message
	 * @param string $type
	 */
	static public function pushMessage($message, $type)
	{
		if (isset($_GET['ajax'])) {
			echo "<div class='flash-".$type."'>".$message."</div>";
		} else {
			Yii::app()->user->setFlash($type, $message);
		}
	}

	/**
	 * Message container.
	 *
	 * This is where the non-ajax messages land through the setFlash() triggered by pushMessage().
	 *
	 * This container is usually rendered somewhere near a grid view.
	 *
	 * @param string $containerId
	 * @return string
	 */
	static public function getMessageContainerHtml($containerId = 'flash-container')
	{
		$containerHtml = '<div id="cbgvm-'.$containerId.'">';

		foreach (self::$messageTypes as $messageType) {
			if(Yii::app()->user->hasFlash($messageType)) {
				$containerHtml .= '<div class="flash-'.$messageType.'">'.Yii::app()->user->getFlash($messageType)."</div>\n";
			}
		}

		$containerHtml .= '</div>';

		return $containerHtml;
	}

	/**
	 * Javascript function to handle messages.
	 *
	 * Used in places like 'afterDelete', of a CButtonColumn of a CGridView, etc.
	 *
	 * @param string $containerId
	 * @return string
	 */
	static public function getJsMessageHandler($containerId = 'flash-container')
	{
		return 'function(link, success, data) {'
			.'if (success) {'
//				.'$("#cbgvm-'.$containerId.'").html(data);'
				.'var messageText = $(data).text().trim();'
				.'if (messageText != "") {'
					.'alert($(data).text());'
				.'}'
			.'}'
		.'}';
	}
}