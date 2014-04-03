<?php
/**
 * Active file folder.
 *
 * An active folder is a web/locally accessible folder with given active file types.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBActiveFileFolder
{
	/**
	 * Local root path.
	 * @var string
	 */
	private $_localRootPath;
	/**
	 * Web root path.
	 * @var string
	 */
	private $_webRootPath;
	/**
	 * Active files.
	 * @var CBActiveFile
	 */
	private $_activeFiles = array();

	/**
	 * Initialize.
	 *
	 * @param string $localRootPath
	 * @param string $webRootPath
	 */
	public function __construct($localRootPath, $webRootPath)
	{
		$this->setLocalRootpath($localRootPath);
		$this->setWebRootpath($webRootPath);
	}

	/**
	 * Add active file.
	 * @param CBActiveFile $activeFile
	 */
	public function addActiveFile($activeFile)
	{
		$activeFile->setWebRootUrl($this->_webRootPath);
		$this->_activeFiles[$activeFile->getKey()] = $activeFile;
	}

	/**
	 * Retrieve full hash of active files.
	 * @return CBActiveFile[]
	 */
	public function &getActiveFiles()
	{
		return $this->_activeFiles;
	}

	/**
	 * Active file by key.
	 * @param string $activeKey
	 * @return CBActiveFile
	 */
	public function &getActiveFileByKey($activeKey)
	{
		return $this->_activeFiles[$activeKey];
	}

	/**
	 * Batch assignment of active filenames.
	 *
	 * @param string[] $activeFilenames Active key to filename hash.
	 */
	public function setActiveFileNames($activeFilenames)
	{
		foreach ($activeFilenames as $activeKey=>$filename) {
			$this->_activeFiles[$activeKey]->setFileName($filename);
		}
	}

	/**
	 * Change web root path.
	 *
	 * This might be necessary if the path depends on some model's id and the folder
	 * was initialized *after* the model received an id.
	 *
	 * @param string $webRootPath
	 */
	public function setWebRootpath($webRootPath)
	{
		$this->_webRootPath = $webRootPath;
	}

	/**
	 * Change local root path.
	 *
	 * This might be necessary if the path depends on some model's id and the folder
	 * was initialized *after* the model received an id.
	 *
	 * @param string $localRootPath
	 */
	public function setLocalRootpath($localRootPath)
	{
		$this->_localRootPath = $localRootPath;
	}

	/**
	 * Folder in which source files are stored.
	 *
	 * Folder path is absolute and can be used for file I/O.
	 *
	 * @return string
	 */
	public function getLocalRootpath()
	{
		return $this->_localRootPath;
	}

	/**
	 * Root url from which files are accessed.
	 *
	 * @return string
	 */
	public function getWebRootpath()
	{
		return $this->_webRootPath;
	}

	/**
	 * Make filename safe.
	 *
	 * @param string $name
	 * @return string
	 */
	public function getSafeFilename($name)
	{
		return strtolower(str_replace(array('+', '/', '\\', '=', ':', '%', '#'), '_', $name));
	}

	/**
	 * Store uploaded files to $basePath.
	 *
	 * @param CUploadedFile[] $uploadedFiles
	 */
	public function storeUploadedFiles($uploadedFiles)
	{
		// Default absolute path for storing files
		$basePath = $this->_localRootPath;

		if (!is_dir($basePath)) {
			if (!mkdir($basePath, 0777, true)) {
				throw new CException('Could not create upload folder "'.$basePath.'"');
			}
		}

		// Set uploaded file names
		foreach ($uploadedFiles as $uploadedName=>$uploadedFile) {
			/* @var $uploadedFile CUploadedFile */
			if ($uploadedFile) {
				if (!$uploadedFile->saveAs($basePath.$this->getSafeFilename($uploadedFile->getName()))) {
					throw new CException('Could not save file "'.$uploadedName.'"');
				}
			}
		}
	}

	/**
	 * Get safe file names of uploaded files.
	 *
	 * @param CUploadedFile[] $uploadedFiles
	 */
	public function getSafeUploadedFileNames($uploadedFiles)
	{
		// Set uploaded file names
		$safeNames = array();
		foreach ($uploadedFiles as $fieldName=>$uploadedFile) {
			/* @var $uploadedFile CUploadedFile */
			if ($uploadedFile) {
				$safeNames[$fieldName] = $this->getSafeFilename($uploadedFile->getName());
			}
		}
		return $safeNames;
	}

	/**
	 * Retrieve and validate uploaded files.
	 *
	 * @param string $namePostfix Optional field name postfix to be appended.
	 * @return CUplodedFile[]
	 */
	public function getUploadedFilesIfValid($namePostfix = '')
	{
		$uploadedFiles = array();
		foreach ($this->_activeFiles as $activeFile) {
			/* @var $activeFile CBActiveFile */
			$uploadedFiles[$activeFile->getKey()] = $activeFile->getUploadedFileIfValid($namePostfix);
		}

		return $uploadedFiles;
	}
}
