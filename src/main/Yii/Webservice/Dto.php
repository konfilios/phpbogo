<?php
/*
 */

namespace Bogo\Yii\Webservice;
use Bogo\Types;

/**
 * Base class for data transfer objects.
 *
 * @since 1.1
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Dto extends \CFormModel implements Types\ITypedObject
{
	use Types\TTypedObject;

	/**
	 * Throw exception if validation fails.
	 *
	 * @param array $attributes
	 * @param boolean $clearErrors
	 * @return boolean
	 * @throws \CException
	 */
	public function validate($attributes = null, $clearErrors = true)
	{
		if (parent::validate($attributes, $clearErrors)) {
			return true;
		}

		$allErrors = array();
		foreach ($this->getErrors() as $attrName=>$attrErrors) {
			$allErrors = array_merge($allErrors, $attrErrors);
		}

		throw new \CException(implode("\n", $allErrors));
	}
}
