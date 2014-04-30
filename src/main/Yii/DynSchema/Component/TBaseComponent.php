<?php
/*
 */

namespace Bogo\Yii\DynSchema\Component;

/**
 * Base trait shared among all kinds of utilities.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
trait TBaseComponent
{
	/**
	 * @inheritdoc
	 */
	public function getId()
	{
		return get_class($this);
	}

	/**
	 * Collection type of values.
	 *
	 * @var string
	 */
	public $valueCollectionType;

	/**
	 * Assert valueCollectionType is among supported ones.
	 *
	 * @param array $supportedCollectionTypes
	 * @throws Exception
	 */
	protected function validateValueCollectionType(array $supportedCollectionTypes)
	{
		if (!in_array($this->valueCollectionType, $supportedCollectionTypes)) {
			throw new Exception('Value collection type "'.$this->valueCollectionType
				.'" is not supported by "'.get_class($this).'"');
		}
	}
}
