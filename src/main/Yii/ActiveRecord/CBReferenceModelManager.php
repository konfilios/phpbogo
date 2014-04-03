<?php
/**
 * Reference model manager.
 *
 * @since 1.4
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class CBReferenceModelManager extends CApplicationComponent
{
	/**
	 * Reference model classes.
	 *
	 * @var string[]
	 */
	public $modelClasses;

	/**
	 * Constant name from model.
	 *
	 * @param CActiveRecord $model
	 * @return string
	 */
	private function getConstantName(CActiveRecord $model)
	{
		return strtoupper(str_replace(array(' ', '-', ':', '=', '+'), '_', $model->title));
	}

	/**
	 * Javascript comment form model.
	 *
	 * @param CActiveRecord $model
	 * @param string $padding
	 * @return string
	 */
	private function getJavascriptComment(CActiveRecord $model, $padding = '')
	{
		return $padding."/**\n"
				.$padding." * ".$model->notes."\n"
				.$padding." */\n";
	}

	/**
	 * Javascript field declaration.
	 *
	 * @param CActiveRecord $model
	 * @param string $padding
	 * @return string
	 */
	private function getJavascriptField(CActiveRecord $model, $padding = '')
	{
		return $padding.$this->getConstantName($model).': '.json_encode($model->id);
	}

	/**
	 * Outputs models as javascript object literals.
	 *
	 * @param string $varNamePrefix
	 * @return string
	 */
	public function toJavascript($varNamePrefix = '')
	{
		$javascript = '';
		$padding = str_repeat("\t", 1);
		foreach ($this->modelClasses as $modelClass) {
			$finder = $modelClass::model();

			$javascriptFields = array();

			foreach ($finder->findAll() as $model) {
				$javascriptFields[] = $this->getJavascriptComment($model, $padding)
					.$this->getJavascriptField($model, $padding);
			}
			$javascript .= "var ".$varNamePrefix.$modelClass." = {\n"
					.implode(",\n", $javascriptFields)."\n"
					."};\n\n";
		}
		return $javascript;
	}
}