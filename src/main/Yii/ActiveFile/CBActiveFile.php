<?php
/**
 * Active File with validations.
 *
 * @property string $label Label for the file. Appears in input forms.
 * @property string[] $extensions Array of valid extensions.
 * @property string $extensionsCsv Csv representation of $extensions.
 * @property string $inputFileFieldName Html name for input file field.
 *
 * @since 1.1
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBActiveFile extends CComponent
{
	/**
	 * Active file key.
	 * @var string
	 */
	private $_key;
	/**
	 * File name (without web root).
	 * @var string
	 */
	private $_fileName;
	/**
	 * Web root (for urls)
	 * @var string
	 */
	private $_webRootUrl;
	/**
	 * Label for field.
	 * @var string
	 */
	private $_label = null;
	/**
	 * Csv of allowed extensions.
	 * @var string
	 */
	private $_extensions = array();
	/**
	 * Description of field.
	 * @var string
	 */
	public $description;
	/**
	 * Content type.
	 *
	 * Eg. image, audio, etc.
	 * @var string
	 */
	public $contentType;
	/**
	 * Exact pixel width.
	 * @var integer
	 */
	public $pixelWidth;
	/**
	 * Exact pixel height.
	 * @var integer
	 */
	public $pixelHeight;
	/**
	 * Maximum pixel width.
	 * @var integer
	 */
	public $maxPixelWidth;
	/**
	 * Maximum pixel height.
	 * @var integer
	 */
	public $maxPixelHeight;
	/**
	 * Maximum file size in bytes.
	 * @var integer
	 */
	public $maxBytesize;

	/**
	 * Set manual label.
	 * @param string $label
	 */
	public function setLabel($label)
	{
		$this->_label = $label;
	}

	/**
	 * Get label.
	 * @return string
	 */
	public function getLabel()
	{
		return $this->_label;
	}

	/**
	 * Get key.
	 * @return string
	 */
	public function getKey()
	{
		return $this->_key;
	}

	/**
	 * Set key.
	 * @param string $key
	 */
	public function setKey($key)
	{
		$this->_key = $key;
	}

	/**
	 * Set file name.
	 * @param string $fileName
	 */
	public function setFileName($fileName)
	{
		$this->_fileName = $fileName;
	}

	/**
	 * Get file name.
	 * @return type
	 */
	public function getFileName()
	{
		return $this->_fileName;
	}

	/**
	 * Get file url.
	 * @return string
	 */
	public function getFileUrl()
	{
		return $this->_fileName ? $this->_webRootUrl.$this->_fileName : '';
	}

	/**
	 * Get web root url.
	 * @return string
	 */
	public function getWebRootUrl()
	{
		return $this->_webRootUrl;
	}

	/**
	 * Set web root url.
	 * @return string
	 */
	public function setWebRootUrl($webRootUrl)
	{
		$this->_webRootUrl = $webRootUrl;
	}

	/**
	 * Html name for input file field.
	 * @return string
	 */
	public function setInputFileFieldName($fileFieldName)
	{
		$this->inputFileFieldName = $fileFieldName;
	}

	/**
	 * Html name for input file field.
	 * @return string
	 */
	public function getInputFileFieldName()
	{
		return $this->inputFileFieldName;
	}

	/**
	 * Array of allowed extensions.
	 * @return string[]
	 */
	public function getExtensions()
	{
		return $this->_extensions;
	}

	/**
	 * Set extensions csv.
	 * @param string $extensionsCsv
	 */
	public function setExtensionsCsv($extensionsCsv)
	{
		$this->_extensions = array();
		foreach (explode(',', $extensionsCsv) as $extension) {
			$extension = trim($extension);

			if ($extension{0} === '.') {
				$extension = substr($extension, 1);
			}

			$this->_extensions[] = strtolower($extension);
		}
	}

	/**
	 * Get extensions csv.
	 * @return string
	 */
	public function getExtensionsCsv()
	{
		return implode(', ', $this->_extensions);
	}

	/**
	 * Validation rules.
	 * @return array[]
	 */
	public function rules()
	{
		return array(
			array('label, contentType, extensionsCsv, pixelWidth, pixelHeight, maxPixelWidth, maxPixelHeight, description, maxBytesize', 'safe')
		);
	}

	/**
	 * Human readable list of restrictions.
	 * @return string
	 */
	public function getRestrictionsText()
	{
		$restrictions = array();

		if ($this->maxBytesize) {
			$restrictions[] = 'Max file size: '.Yii::app()->format->size($this->maxBytesize);
		}

		if ($this->extensionsCsv) {
			$restrictions[] = 'File types: '.$this->extensionsCsv;
		}

		if ($this->pixelWidth && $this->pixelHeight) {
			$restrictions[] = 'Dimensions: '.$this->pixelWidth.'x'.$this->pixelHeight;

		} else if ($this->maxPixelWidth && $this->maxPixelHeight) {
			$restrictions[] = 'Max Dimensions: '.$this->maxPixelWidth.'x'.$this->maxPixelHeight;
		}

		return implode(",\n", $restrictions);
	}

	/**
	 * Produce an html upload file field.
	 * @param string $namePostfix Optional field name postfix to be appended.
	 * @param string $value Value.
	 * @param array $htmlOptions Extra html options.
	 * @return string
	 */
	public function getUploadFileField($namePostfix = '', $value = '', $htmlOptions = array())
	{
		return CHtml::fileField($this->getInputFileFieldName().$namePostfix, $value, $htmlOptions);
	}

	/**
	 * Retrieve and validate uploaded file.
	 *
	 * @param string $namePostfix Optional field name postfix to be appended.
	 * @return CUploadedFile
	 */
	public function getUploadedFileIfValid($namePostfix = '')
	{
		$uploadedFile = CUploadedFile::getInstanceByName($this->getInputFileFieldName().$namePostfix);

		if (empty($uploadedFile)) {
			return null;
		}

		// Check upload error
		if ($uploadedFile->hasError) {
			throw new CHttpException(400, 'Failed to upload "'.$this->getLabel().'" (code: '.$uploadedFile->error.')');
		}

		// Check file size validity
		if (!empty($this->maxBytesize) && ($uploadedFile->size > $this->maxBytesize)) {
			throw new CHttpException(400, 'Uploaded file "'.$this->getLabel()
					.'" is '.Yii::app()->format->size($uploadedFile->size).' while the limit is '.Yii::app()->format->size($this->maxBytesize));
		}

		// Check extension validity
		if (!empty($this->_extensions) && !in_array(strtolower($uploadedFile->extensionName), $this->_extensions)) {
			throw new CHttpException(400, 'Uploaded file "'.$this->getLabel().'" may only be of specific types: '.$this->extensionsCsv);
		}

		if ($this->pixelWidth || $this->pixelHeight || $this->maxPixelWidth || $this->maxPixelHeight) {
			list($actualWidth, $actualHeight, $actualType, $actualAttr) = getimagesize($uploadedFile->tempName);

			$actualWidth = intval($actualWidth);
			$actualHeight = intval($actualHeight);

			if ($actualWidth && $actualHeight) {

				// Exact width
				if ($this->pixelWidth && ($actualWidth != $this->pixelWidth)) {
					throw new CHttpException(400, 'Uploaded file "'.$this->getLabel()
							.'" width should be exactly '.$this->pixelWidth.'px, not '.$actualWidth.'px');
				}

				// Exact height
				if ($this->pixelHeight && ($actualHeight != $this->pixelHeight)) {
					throw new CHttpException(400, 'Uploaded file "'.$this->getLabel()
							.'" height should be exactly '.$this->pixelHeight.'px, not '.$actualHeight.'px');
				}

				// Max width
				if ($this->maxPixelWidth && ($actualWidth > $this->maxPixelWidth)) {
					throw new CHttpException(400, 'Uploaded file "'.$this->getLabel()
							.'" width should be at most '.$this->maxPixelWidth.'px, not '.$actualWidth.'px');
				}

				// Max height
				if ($this->maxPixelHeight && ($actualHeight > $this->maxPixelHeight)) {
					throw new CHttpException(400, 'Uploaded file "'.$this->getLabel()
							.'" height should be at most '.$this->maxPixelHeight.'px, not '.$actualHeight.'px');
				}
			}
		}

		return $uploadedFile;
	}
}
