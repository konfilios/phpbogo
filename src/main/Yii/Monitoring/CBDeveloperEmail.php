<?php
/**
 * Email sent out to developers with several software-internal information.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBDeveloperEmail
{
	/**
	 * Rotating list of auto-colors.
	 *
	 * @var string[]
	 */
	static private $colors = array(
		'white' => '#FFF',
		'second' => '#FFFCE3',
		'third' => '#E0EAFF',
		'fourth' => '#E0FFF1',
	);

	/**
	 * Error color.
	 *
	 * Prefer this one for consistency.
	 *
	 * @var string
	 */
	static public $colorError = '#F7E6EA';

	/**
	 * Sender email address.
	 *
	 * @var string
	 */
	public $from = '';

	/**
	 * Recipient email addresses.
	 *
	 * @var string[]
	 */
	public $to;

	/**
	 * Email subject.
	 *
	 * @var string
	 */
	public $subject = '';

	/**
	 * Content sections.
	 *
	 * @var array[]
	 */
	private $sections = array();

	/**
	 * Wrap $rows (with label $title) into a div with color $color.
	 *
	 * @param string $title Title/label of notification data segment.
	 * @param array  $rows  List of data belonging to segment.
	 * @param string $color Background color of segment (defaults to white).
	 *
	 * @return string
	 */
	public function pushSection($title, array $rows, $color = '#FFF')
	{
		$this->sections[] = array(
			'title' => $title,
			'rows' => $rows,
			'color' => $color,
		);
	}

	/**
	 * Push many sections at one call.
	 *
	 * @param array $sections
	 */
	public function pushSections(array $sections = array())
	{
		$colors = null;
		foreach ($sections as $title=>$rows) {
			if (empty($colors)) {
				// Re-fill colors
				$colors = self::$colors;
			}
			// Get the first and remove
			$color = array_shift($colors);

			$this->pushSection($title, $rows, $color);
		}
	}

	/**
	 * Send email.
	 *
	 * @return boolean
	 */
	public function send()
	{
		if (empty($this->from) || empty($this->to)) {
			// Not enough addressed given, probably on purpose
			error_log($this->getRawBody());
		} else {
			if (is_array($this->to)) {
				$emailsTo = implode(',', $this->to);
			} else {
				$emailsTo = $this->to;
			}

			// Headers
			$emailHeaders = array(
				'From: '.$this->from,
				'MIME-Version: 1.0',
				'Content-type: text/html; charset=utf-8'
			);

			return mail($emailsTo, $this->subject, $this->getHtmlBody(), implode("\r\n", $emailHeaders));
		}
	}

	/**
	 * Raw body to be used in error_log().
	 *
	 * @return string
	 */
	public function getRawBody()
	{
		$body = '';

		foreach ($this->sections as &$section) {
//			$style = 'style="background-color: '.$color.'; padding: 20px; border-bottom: 3px double black"';
			$body .= '=== '.$section['title']." ===\n";
			foreach ($section['rows'] as $label => $row) {
				if (is_object($row) || is_array($row)) {
					$row = print_r($row, true);
				}

				$body .= '['.$label."]\n".$row."\n";
			}
			$body .= "\n";
		}
		return $body;
	}

	/**
	 * Html-formatted body to be used in email.
	 *
	 * @return string
	 */
	public function getHtmlBody()
	{
		$body = '';

		foreach ($this->sections as &$section) {
//			$style = 'style="background-color: '.$color.'; padding: 20px; border-bottom: 3px double black"';
			$sectionContent = '';
			foreach ($section['rows'] as $label => $row) {
				if (is_object($row) || is_array($row)) {
					$row = htmlspecialchars(print_r($row, true));
				} else {
					$row = htmlspecialchars($row);
				}

				$sectionContent .= '<tr>'
						.'<td valign="top" width="140" style="padding: 10px"><b>'.$label.'</b></td>'
						.'<td style="padding: 10px">'."\n<pre>".$row."</pre>\n".'</td>'
						.'</tr>';
			}

			$body .= '<h2>'.$section['title'].'</h2>'
					.'<table style="background-color: '.$section['color'].'; width: 100%">'
					.$sectionContent
					.'</table><br />';
		}
		return '<html><body>'.$body.'</body></html>';
	}
}
