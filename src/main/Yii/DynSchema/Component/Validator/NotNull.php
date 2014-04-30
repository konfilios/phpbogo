<?php
/*
 */

namespace Bogo\Yii\DynSchema\Component\Validator;

/**
 * Asserts a value is not null.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class NotNull
{
	/**
	 * @inheritdoc
	 */
	public function getErrors($value)
	{
		$errors = array();

		if ($value === null) {
			$errors[] = Yii::t('DynSchema.Validator',
				'Cannot be null');
		}

		return $errors;
	}
	
}
