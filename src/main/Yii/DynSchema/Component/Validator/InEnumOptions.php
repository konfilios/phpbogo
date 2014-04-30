<?php
/*
 */

namespace Bogo\Yii\DynSchema\Component\Validator;

use Bogo\Yii\DynSchema\Component\TEnumComponent;

/**
 * Asserts a value belongs to a predefined enumeration.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class InEnumOptions extends Base
{
	use TEnumComponent;

	/**
	 * @inheritdoc
	 */
	public function getErrors($value)
	{
		$errors = array();

		if ($value === null) {
			return $errors;
		}
		
		if (is_array($value)) {
			$valueArray = $value;
		} else {
			$valueArray = array($value);
		}

		foreach ($valueArray as $valueItem) {
			if (!isset($this->enumOptions[$valueItem])) {
				$errors[] = Yii::t('DynSchema.Validator',
					'Value "{value}" is not valid', array(
						'{value}' => $valueItem
					));
			}
		}

		return $errors;
	}
}
