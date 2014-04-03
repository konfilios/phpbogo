<?php
/* 
 */

namespace Bogo\Yii\Webservice\Server;

class RestfulController extends Controller
{
	/**
	 * Lookup a model by pk.
	 *
	 * Optionally throw exception if it's not found.
	 *
	 * @param CActiveRecord $finder
	 * @param mixed $pk
	 * @param boolean $throwExceptionIfNotFound
	 * @return CActiveRecord
	 * @throws CHttpException
	 */
	protected function findModelByPk(\CActiveRecord $finder, $pk, $throwExceptionIfNotFound = true)
	{
		// Find the model
		$model = $finder->findByPk($pk);

		// Make sure it's found
		if (empty($model) && $throwExceptionIfNotFound) {
			throw new \CHttpException(404, get_class($finder).' with id "'.$pk.'" not found');
		}

		return $model;
	}
}
