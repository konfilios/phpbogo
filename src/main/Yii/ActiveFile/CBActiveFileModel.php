<?php
/**
 * Active file from an active record model.
 *
 * @since 1.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBActiveFileModel extends CBActiveFile
{
	/**
	 * Parent model.
	 *
	 * @var CModel
	 */
	private $_model;

	/**
	 * Field name.
	 * @var string
	 */
	private $_modelAttributeNameBase;
	/**
	 * Field name for relative path.
	 * @var string
	 */
	private $_modelAttributeNamePath;
	/**
	 * Field name for absolute url.
	 * @var string
	 */
	private $_modelAttributeNameUrl;

	/**
	 * Initialize.
	 *
	 * @param CModel $model Parent model.
	 * @param string $modelFieldNameBase Active file name.
	 */
	public function __construct(CModel $model, $modelFieldNameBase)
	{
		$this->_model = $model;
		$this->_modelAttributeNameBase = $modelFieldNameBase;
		$this->_modelAttributeNamePath = $modelFieldNameBase.'Path';
		$this->_modelAttributeNameUrl = $modelFieldNameBase.'Url';
		$this->setInputFileFieldName(CHtml::resolveName($this->_model, $this->_modelAttributeNamePath));
	}

	/**
	 * Url field name.
	 * @return string
	 */
//	public function getAttributeNameUrl()
//	{
//		return $this->_modelAttributeNameUrl;
//	}

	/**
	 * Path field name.
	 * @return string
	 */
	public function getAttributeName()
	{
		return $this->_modelAttributeNamePath;
	}

	/**
	 *
	 * @return CModel
	 */
	public function getModel()
	{
		return $this->_model;
	}

	/**
	 * Url from owen model.
	 * @return string
	 */
	public function getValue()
	{
		return $this->_model->{$this->_modelAttributeNamePath};
	}

	/**
	 * Url from owen model.
	 * @return string
	 */
	public function getUrlValue()
	{
		return $this->_model->{$this->_modelAttributeNameUrl};
	}

	/**
	 * Get label.
	 * @return string
	 */
	public function getLabel()
	{
		return parent::getLabel() ?: $this->_model->getAttributeLabel($this->_modelAttributeNamePath);
	}
}
