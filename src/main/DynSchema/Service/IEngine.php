<?php
/*
 */

namespace Bogo\DynSchema\Service;

use \Bogo\DynSchema\Core\IAttribute;

/**
 * DynSchema Engine.
 *
 * @since 2.0
 * @package Components
 * @author Konstantinos Filios <konfilios@gmail.com>
 */
interface IEngine
{
	/**
	 * Register a whole spec.
	 * 
	 * @param array $spec
	 */
	public function registerSpec($spec);

	/**
	 * Return attribute instance by id.
	 *
	 * @param string|integer $attrId
	 * @return IAttribute
	 * @throws Exception
	 */
	public function getAttribute($attrId);
	/**
	 * Create an attribute.
	 *
	 * @param array $spec
	 * @return IAttribute
	 */
//	public function createAttribute($spec);

	/**
	 * Create an attribute type.
	 *
	 * @param array $spec
	 * @return IAttributeType
	 */
//	public function createAttributeType($spec);

	/**
	 * Create a presentation widget.
	 *
	 * @param IAttribute $attribute Attribute for which the widget is created.
	 * @param array $spec Widget specification.
	 * @return IWidgetComponent
	 */
	public function createWidget(IAttribute $attribute, $spec);

	/**
	 * Create a validator according to given specifications.
	 *
	 * @param IAttribute $attribute Attribute for which the constraint is created.
	 * @param array $spec
	 * @return IConstraintComponent
	 */
	public function createConstraint(IAttribute $attribute, $spec);
}
