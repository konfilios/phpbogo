<?php
/**
 * Active Record attributes with active file functionality.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
trait TBActiveRecordWithFileAttributes
{
	/**
	 * Local root path of model's file folder.
	 *
	 * @return string
	 */
	abstract public function getLocalRootPath();

	/**
	 * Web root path of model's file folder.
	 *
	 * @return string
	 */
	abstract public function getWebRootPath();

	/**
	 * File field attributes.
	 *
	 * Used for validations and form rendering.
	 *
	 * @var array[]
	 */
	abstract public function fileFieldAttributes();

	/**
	 * File folder containing attribute model files.
	 *
	 * @var CBActiveFileFolder
	 */
	private $_attributeFileFolder = null;

	/**
	 * Retrieve active folder singleton.
	 * @return CBActiveFileFolder
	 */
	public function getAttributeFileFolder()
	{
		if ($this->_attributeFileFolder === null) {

			$this->_attributeFileFolder = new CBActiveFileFolder($this->getLocalRootPath(), $this->getWebRootPath());

			// Added active files
			foreach ($this->fileFieldAttributes() as $activeFieldBaseName=>$activeAttributes) {
				// Create
				$activeFile = new CBActiveFileAttribute($this, $activeFieldBaseName);

				// Assign attributes
				foreach ($activeAttributes as $attrName=>$attrValue) {
					$activeFile->$attrName = $attrValue;
				}

				// Save
				$this->_attributeFileFolder->addActiveFile($activeFile);
			}
		}
		return $this->_attributeFileFolder;
	}

	/**
	 * Refresh folder paths after initialization.
	 */
	public function refreshFolderPaths()
	{
		if ($this->_attributeFileFolder !== null) {
			$this->_attributeFileFolder->setLocalRootPath($this->getLocalRootPath());
			$this->_attributeFileFolder->setWebRootPath($this->getWebRootPath());
		}
	}

	/**
	 * List of active files.
	 *
	 * @return CBActiveFileAttribute[]
	 */
	public function activeFiles()
	{
		return $this->getAttributeFileFolder()->getActiveFiles();
	}

	/**
	 * Create active file.
	 *
	 * @param string $activeName
	 * @return CBActiveFileAttribute
	 */
	public function activeFileByKey($activeName)
	{
		return $this->getAttributeFileFolder()->getActiveFileByKey($activeName);
	}

	/**
	 * Set field values from uploaded files.
	 *
	 * @param CUploadedFile[] $uploadedFiles
	 */
	public function setUploadedFileValues($uploadedFiles)
	{
		$this->attributes = $this->getAttributeFileFolder()->getSafeUploadedFileNames($uploadedFiles);
	}

	/**
	 * Store uploaded files to $basePath.
	 *
	 * @param CUploadedFile[] $uploadedFiles
	 */
	public function storeUploadedFiles($uploadedFiles)
	{
		// Make sure folder paths are up-to-date
		$this->refreshFolderPaths();
		// Store the files
		$this->getAttributeFileFolder()->storeUploadedFiles($uploadedFiles);
	}

	/**
	 * Absolute local path from file field.
	 * @param string $fieldName
	 * @return string
	 */
	public function getFileFieldAbsolutePath($fieldName)
	{
		// Empty file paths have empty urls
		$filePath = $this->$fieldName;
		if (empty($filePath)) {
			return null;
		} else {
			return $this->getAttributeFileFolder()->getLocalRootPath().$filePath;
		}
	}

	/**
	 * Url from file field.
	 *
	 * @param string $fieldName
	 * @return string
	 */
	public function getFileFieldUrl($fieldName)
	{
		// Empty file paths have empty urls
		$filePath = $this->$fieldName;
		if (empty($filePath)) {
			return null;
		} else {
			return $this->getAttributeFileFolder()->getWebRootPath().$filePath;
		}
	}

	/**
	 * Retrieve and validate uploaded files.
	 *
	 * @param string $namePostfix Optional field name postfix to be appended.
	 * @return CUplodedFile[]
	 */
	public function getUploadedFilesIfValid($namePostfix = '')
	{
		return $this->getAttributeFileFolder()->getUploadedFilesIfValid($namePostfix);
	}

	/**
	 * Dynamic *Url fields exist if corresponding *Path exist.
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function __isset($name)
	{
		if (parent::__isset($name)) {
			// Default behaviour first
			return true;
		}

		// If it's a *Url check if there's a corresponding *Path.
		return ((substr($name, -3) === 'Url') && parent::__isset(substr($name, 0, -3).'Path'));
	}

	/**
	 * Dynamic *Url value from corresponding *Path.
	 *
	 * @param string $name
	 * @return string
	 */
	public function __get($name)
	{
		if ((substr($name, -3) === 'Url') && !parent::__isset($name)) {
			// It's a dynamic url field
			return $this->getFileFieldUrl(substr($name, 0, -3).'Path');
		}

		// Default behavior
		return parent::__get($name);
	}
}