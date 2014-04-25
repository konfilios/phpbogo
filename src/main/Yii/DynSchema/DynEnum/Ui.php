<?php
/*
 */

namespace Bogo\Yii\DynSchema\DynEnum;

use \Bogo\DynSchema\IDatatype;
use \CHtml;

/**
 * Call event.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
class Ui
{
	public function inputWidget(IDatatype $datatype, $name, $value, $params = array())
	{
		/* @var $datatype \Bogo\Yii\DynSchema\DynEnum\Dataset */
		if ($datatype->getCollectionType() != IDatatype::COLLECTION_TYPE_NONE) {
			$params['multiple'] = 'multiple';
		}
		return CHtml::dropDownList($name, $value, $datatype->getEnumOptions(), $params);
	}
}
