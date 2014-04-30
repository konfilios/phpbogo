<?php
/*
 */

namespace Bogo\Yii\DynSchema\Component\Validator;

/**
 * Asserts a value is not empty.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class NotEmpty
{
	/**
	 * @inheritdoc
	 */
	public function getErrors($value)
	{
		$errors = array();

		if (empty($value)) {
			$errors[] = Yii::t('DynSchema.Validator',
				'Cannot be empty');
		}

		return $errors;
	}
	
}
