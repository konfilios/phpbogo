<?php
/**
 * Full quiz details widget.
 */
class CBActiveFileInputWidget extends CWidget
{
	/**
	 * Active file.
	 * @var CBActiveFileAttribute
	 */
	public $activeFile;

	/**
	 * Show restrictions?
	 * @var boolean
	 */
	public $renderRestrictions = true;

	/**
	 * Show input field?
	 * @var boolean
	 */
	public $renderInputField = true;

	/**
	 * Show preview?
	 * @var boolean
	 */
	public $renderPreview = true;

	/**
	 * Ajax file input options.
	 * @var array
	 */
	public $ajaxOptions = array();

	/**
	 * Render.
	 */
	public function run()
	{
		$fieldId = $this->activeFile->getInputFileFieldName();

		echo CHtml::label($this->activeFile->label, $fieldId);
		echo '<p>'.$this->activeFile->description.'</p>';

		if ($this->renderRestrictions) {
			echo '<div><i>'.$this->activeFile->getRestrictionsText().'</i></div>';
		}

		if ($this->renderInputField) {
			if ($this->renderInputField === 'ajax') {
				$ajaxValidation = array();

				if (!empty($this->activeFile->extensions)) {
					$ajaxValidation['allowedExtensions'] = $this->activeFile->extensions;
				}
				if (!empty($this->activeFile->maxBytesize)) {
					$ajaxValidation['sizeLimit'] = $this->activeFile->maxBytesize;
				}
						// //array("jpg","jpeg","gif","exe","mov" and etc...
//						'allowedExtensions'=>array("jpg"),
						// maximum file size in bytes
//						'sizeLimit' => 10*1024*1024,
						// minimum file size in bytes
//						'minSizeLimit' => 0,
//						'itemLimit' => 0,
//						'stopOnFirstInvalidFile': true


				$ajaxOptions = CMap::mergeArray(array(
					'validation' => $ajaxValidation,
					'template' => '<div class="qq-uploader">'
							.'<pre class="qq-upload-drop-area"><span>{dragZoneText}</span></pre>'
							.'<div class="qq-upload-button btn btn-success" style="width: auto;">{uploadButtonText}</div>'
							.'<span class="qq-drop-processing"><span>{dropProcessingText}</span><span class="qq-drop-processing-spinner"></span></span>'
							.'<ul class="qq-upload-list" style="margin-top: 10px; text-align: center;"></ul>'
						.'</div>',
					'classes' => array(
						'success' => 'alert alert-success',
						'fail' => 'alert alert-error'
					)
				), $this->ajaxOptions);

				$this->controller->widget('ext.EAjaxUpload.EAjaxUploadWidget', array(
					'id'=>CHtml::getIdByName($fieldId.'_AjaxUpload'),
					'config'=>$ajaxOptions
				));
			} else {
				echo $this->activeFile->getUploadFileField();
			}
		}

		if ($this->renderPreview) {
			if ( ($previewUrl = $this->activeFile->getFileUrl())) {
				echo '<div>'.CHtml::link('<i class="icon-eye-open"></i> Preview existing file', $previewUrl, array('target'=>'_blank')).'</div>';
			}
		}
	}
}
