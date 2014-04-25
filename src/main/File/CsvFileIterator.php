<?php
namespace Bogo\File;

/**
 * CSV file iterator.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CsvFileIterator extends TextFileIterator {
	/**
	 * If true, the first line's fields are used for keys.
	 * 
	 * Furthermore, the first line is skipped.
	 * 
	 * @var boolean
	 */
	public $useFirstLineForKeys = true;

	/**
	 * Field delimiter.
	 *
	 * @var string
	 */
	public $fieldDelimiter = ";";

	/**
	 * Keys retrieved from first line.
	 * 
	 * This is only set if useFirstLineAsKeys is true.
	 *
	 * @var array
	 */
	private $keys;
	
	/**
	 * Offset coming from skipped rows.
	 *
	 * @var integer
	 */
	private $keyOffset = 0;

	public function next() {
		parent::next();

		if ($this->useFirstLineForKeys && $this->valid() && parent::key() == 0) {
			$firstLine = parent::current();
			$this->keys = explode($this->fieldDelimiter, rtrim($firstLine));
			$this->keyOffset++;
			parent::next();
		}
	}
	
	public function key() {
		return parent::key() - $this->keyOffset;
	}

	public function current() {
		$currentLine = parent::current();

		if ($currentLine) {
			$currentFields = explode($this->fieldDelimiter, rtrim($currentLine));

			if ($this->useFirstLineForKeys) {
				return array_combine($this->keys, $currentFields);
			} else {
				return $currentFields;
			}
		} else {
			return array();
		}
	}
}
